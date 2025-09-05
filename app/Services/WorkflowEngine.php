<?php

namespace App\Services;

use App\Models\Workflow;
use App\Models\EmailTemplate;
use App\Mail\TemplateMailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WorkflowEngine
{
    /**
     * Metodo pubblico principale che avvia il controllo dei workflow per un modello.
     */
    public function run(Model $model): void
    {
        $workflows = Workflow::where('trigger_model', get_class($model))
                             ->where('is_active', true)
                             ->get();

        if ($workflows->isEmpty()) {
            return;
        }

        foreach ($workflows as $workflow) {
            $field = $workflow->trigger_condition_field;

            if ($model->wasChanged($field) || $model->wasRecentlyCreated) {
                $newValue = $model->{$field};
                $oldValue = $model->getOriginal($field);

                $newConditionMet = $this->checkCondition($workflow, $newValue);
                $oldConditionMet = $this->checkCondition($workflow, $oldValue);

                if ($newConditionMet && !$oldConditionMet) {
                    Log::info("Workflow #{$workflow->id} ('{$workflow->name}') attivato per " . class_basename($model) . " #{$model->id}.");
                    $this->executeAction($workflow, $model);
                }
            }
        }
    }

    /**
     * Tutti i metodi seguenti sono privati e usati solo da questa classe,
     * eliminando qualsiasi problema di visibilità.
     */
    private function checkCondition(Workflow $workflow, $modelValue): bool
    {
        $operator = $workflow->trigger_condition_operator;
        $workflowValue = $workflow->trigger_condition_value;

        if (strtotime($workflowValue) !== false && !is_numeric($workflowValue)) {
            try {
                $modelDate = Carbon::parse($modelValue);
                $workflowDate = Carbon::parse($workflowValue);
                return match ($operator) {
                    '=' => $modelDate->isSameDay($workflowDate),
                    '!=' => !$modelDate->isSameDay($workflowDate),
                    '>' => $modelDate->isAfter($workflowDate),
                    '<' => $modelDate->isBefore($workflowDate),
                    '>=' => $modelDate->isSameDayOrAfter($workflowDate),
                    '<=' => $modelDate->isSameDayOrBefore($workflowDate),
                    default => false,
                };
            } catch (\Exception $e) { return false; }
        } elseif (is_numeric($modelValue) && is_numeric($workflowValue)) {
            $modelNum = (float)$modelValue; $workflowNum = (float)$workflowValue;
            return match ($operator) {
                '=' => $modelNum == $workflowNum, '!=' => $modelNum != $workflowNum,
                '>' => $modelNum > $workflowNum, '<' => $modelNum < $workflowNum,
                '>=' => $modelNum >= $workflowNum, '<=' => $modelNum <= $workflowNum,
                default => false,
            };
        } else {
            $normalizedModel = preg_replace('/[^a-z0-9]/i', '', strtolower(trim((string)$modelValue)));
            $normalizedWorkflow = preg_replace('/[^a-z0-9]/i', '', strtolower(trim((string)$workflowValue)));
            return match ($operator) {
                '=' => $normalizedModel === $normalizedWorkflow, '!=' => $normalizedModel !== $normalizedWorkflow,
                default => false,
            };
        }
    }

    private function executeAction(Workflow $workflow, Model $model): void
    {
        switch ($workflow->action_type) {
            case 'create_activity':
                $this->createActivityAction($workflow->action_parameters, $model);
                break;
            case 'send_email':
                $this->sendEmailFromTemplateAction($workflow, $model);
                break;
            case 'add_to_marketing':
                $this->addToMarketingAction($workflow->action_parameters, $model);
                break;
        }
    }
    
    private function sendEmailFromTemplateAction(Workflow $workflow, Model $model): void
    {
        $params = $workflow->action_parameters;
        if (empty($params['template_id'])) {
            Log::warning("Workflow #{$workflow->id}: impossibile inviare email, ID del template non specificato.");
            return;
        }
        if (!isset($model->email) || empty($model->email)) {
            Log::warning("Workflow #{$workflow->id}: impossibile inviare email, il record non ha un indirizzo email.");
            return;
        }
        $template = EmailTemplate::find($params['template_id']);
        if (!$template) {
            Log::error("Workflow #{$workflow->id}: Template Email con ID {$params['template_id']} non trovato.");
            return;
        }
        $subject = $this->renderTemplate($template->subject, $model);
        $body = $this->renderTemplate($template->body, $model);
        try {
            Mail::to($model->email)->send(new TemplateMailable($subject, $body));
            Log::info("Workflow #{$workflow->id}: Email con template '{$template->name}' inviata a {$model->email}.");
        } catch(\Exception $e) {
            Log::error("Errore invio email da workflow #{$workflow->id}: " . $e->getMessage());
        }
    }

    private function renderTemplate(string $content, Model $model): string
    {
        preg_match_all('/{{\s*(\w+)\.(\w+)\s*}}/', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $placeholder = $match[0]; $field = $match[2];
            if (isset($model->$field)) {
                $content = str_replace($placeholder, $model->$field, $content);
            }
        }
        return $content;
    }

    private function createActivityAction(array $params, Model $model): void
    {
        if (method_exists($model, 'activities')) {
            $model->activities()->create(['title' => $params['title'] ?? 'Attività generata da workflow', 'type' => $params['type'] ?? 'task', 'start_time' => Carbon::now(), 'status' => 'pending',]);
        }
    }

    private function addToMarketingAction(array $params, Model $model): void
    {
        try {
            if (!empty($params['lists']) && method_exists($model, 'emailLists')) {
                $model->emailLists()->syncWithoutDetaching($params['lists']);
            }
            if (!empty($params['tags']) && method_exists($model, 'tags')) {
                $model->tags()->syncWithoutDetaching($params['tags']);
            }
        } catch (\Exception $e) {
            Log::error("WORKFLOW MARKETING ERROR per " . class_basename($model) . " #{$model->id}: " . $e->getMessage());
        }
    }
}