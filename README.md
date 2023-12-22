# Template progetto Laravel

Questo repository serve come base per la creazione di un progetto `laravel` in cui
i file di storage sono esterni alla base del codice per permettere lo scale dell'applicativo.

## Dipendenze

- [GNU Make](https://www.gnu.org/software/make/)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

> Per i sistemi windows **GNU Make** deve essere installato all'interno di
[WSL](https://learn.microsoft.com/it-it/windows/wsl/about#what-is-wsl-2).

## Installazione

Prima di iniziare controllare il file `.env.example`, in caso una delle porte dichiarate fosse già allocata
copiare il file in nuovo file denominato `.env` e cambiare i valori secondo le proprie necessità.

Per installare il progetto lanciare il comando:

```bash
make install
```

## Struttura cartelle

### .data

La cartella `.data` contiene i file da preservare dei container, come ad esempio i file del servizio
`mysql` in modo da non perdere i dati in caso di distruzione del container.

### .docker

La cartella `.docker` contiene tutti i file di configurazione per i servizi docker.

### code

La cartella `code` contiene i file sorgenti dell'applicativo.

### storage

La cartella `storage` contiene i file di laravel da preservare tra le diverse build.
