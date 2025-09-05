<section>
    <header>
        <h2 class="text-lg font-medium text-dark">
            Elimina Account
        </h2>
        <p class="mt-1 text-sm text-muted">
            Una volta che il tuo account sarà eliminato, tutte le sue risorse e i suoi dati verranno cancellati permanentemente.
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        Elimina Account
    </button>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">Sei sicuro di voler eliminare il tuo account?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-muted">
                            Una volta eliminato, non potrai più tornare indietro. Per favore, inserisci la tua password per confermare.
                        </p>
                        <div class="mt-3">
                            <label for="password_delete" class="form-label visually-hidden">Password</label>
                            <input id="password_delete" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Password">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-danger">Elimina Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>