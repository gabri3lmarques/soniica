class Player {
    constructor() {
        this.audio = new Audio();
        this.currentSong = null;
        this.currentPlaylist = null;
        this.isPlaying = false;
        this.isLooping = false; // Estado do loop
        this.isRandom = false; // Estado do random

        // Elementos do player principal
        this.titleElement = document.querySelector('.current-title');
        this.artistElement = document.querySelector('.current-artist');
        this.timeElement = document.querySelector('.current-time');
        this.totalTimeElement = document.querySelector('.total-time'); // Tempo total
        this.genresElement = document.querySelector('.current-genres');
        this.thumbElement = document.querySelector('.current-thumb');
        this.playPauseButton = document.querySelector('.play-pause');
        this.nextButton = document.querySelector('.next');
        this.previousButton = document.querySelector('.previous');
        this.loopButton = document.querySelector('.loop');
        this.randomButton = document.querySelector('.random'); // Botão de random
        this.progressBarContainer = document.querySelector('.progress-bar-container');
        this.progressBar = document.querySelector('.progress-bar');
        this.volumeSlider = document.querySelector('#volume-slider'); // Slider de volume
        this.volumeIcon = document.querySelector('.volume-icon'); // Slider de volume
        this.barPlayer =  document.querySelector('.sound-wave-player');
        this.addToPlaylistFormContainer = document.querySelector('.add-to-playlist-form-hidden');
        this.tagsContainer = document.querySelector('.current-genres');

        // Event listeners
        this.playPauseButton.addEventListener('click', () => this.togglePlayPause());
        this.nextButton.addEventListener('click', () => this.next());
        this.previousButton.addEventListener('click', () => this.previous());
        this.loopButton.addEventListener('click', () => this.toggleLoop());
        this.randomButton.addEventListener('click', () => this.toggleRandom());
        this.audio.addEventListener('timeupdate', () => this.updateProgressBar());
        this.audio.addEventListener('ended', () => this.handleSongEnd());
        this.volumeSlider.addEventListener('input', (e) => this.adjustVolume(e));
        this.audio.addEventListener('timeupdate', () => this.updateTimeDisplay()); 
        this.audio.addEventListener('loadedmetadata', () => this.setTotalTime());

        // Eventos para a barra de progresso
        this.progressBarContainer.addEventListener('click', (e) => this.seek(e));
        this.progressBarContainer.addEventListener('mousedown', (e) => this.startDrag(e));

        // Inicializar a primeira música automaticamente
        this.initializeFirstSong();
        
        this.audio.volume = this.volumeSlider.value;
    }

    getSongTags() {
        if(this.currentSong) {
                    // Seleciona o elemento com data-tags
        const element = this.currentSong.querySelector('div[data-tags]');

        // Pega o valor do atributo data-tags
        const tagsHTML = element.getAttribute('data-tags');

        // Injeta o conteúdo no outro elemento
        const targetElement = this.tagsContainer;// Selecione o elemento alvo
        targetElement.innerHTML = tagsHTML; // Insere as tags como conteúdo   
        }
    }

    initializeAddToPlaylistForm() {
        if (this.currentSong) {
            // Procurar pelo elemento '.add-to-playlist-form' na música atual
            const addToPlaylistForm = this.currentSong.querySelector('.source');

            if (addToPlaylistForm) {
                // Copiar o conteúdo HTML para o player principal
                const data = addToPlaylistForm.dataset.content;

                if(this.addToPlaylistFormContainer) {
                    this.addToPlaylistFormContainer.innerHTML = data;
                    //this.addToPlaylistFormContainer.classList.add('custom-hidden-select');
                }

            } 
        }
    }

    // Configurar o tempo total da música
    setTotalTime() {
        if (!isNaN(this.audio.duration)) {
            const duration = this.audio.duration;
            this.totalTimeElement.textContent = this.formatTime(duration);
            this.timeElement.textContent = this.formatTime(duration);
            this.getSongTags()
        }
    }

    // Atualizar o tempo restante
    updateTimeDisplay() {
        if (!isNaN(this.audio.duration)) {
            const remainingTime = this.audio.duration - this.audio.currentTime;
            this.timeElement.textContent = this.formatTime(remainingTime); // Exibe o tempo restante
        }
    }

    // Formatar tempo em MM:SS
    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
    }

    
    adjustVolume(event) {
        // Ajustar o volume do elemento de áudio com base no slider
        const newVolume = event.target.value;
        this.audio.volume = newVolume;

        if(newVolume == 0){
            this.volumeIcon.classList.add('zero')
        } else {
            this.volumeIcon.classList.remove('zero')
        }
    }

    initializeFirstSong() {
        const firstPlaylist = document.querySelector('.playlist');
        if (!firstPlaylist) return;

        const firstSong = firstPlaylist.querySelector('.song');
        if (!firstSong) return;

        this.currentPlaylist = firstPlaylist;
        this.currentSong = firstSong;
        this.audio.src = firstSong.dataset.src;

        this.updatePlayerInfo(firstSong);

        // Carregar metadados para calcular a duração
        this.audio.load();

        this.initializeAddToPlaylistForm();

        this.getSongTags();
    }

    toggleRandom() {
        this.isRandom = !this.isRandom;

        // Adicionar ou remover a classe 'active' no botão random
        if (this.isRandom) {
            this.randomButton.classList.add('active');
        } else {
            this.randomButton.classList.remove('active');
        }
    }

    getRandomSong() {
        const songs = [...this.currentPlaylist.querySelectorAll('.song')];
        let randomSong;

        do {
            randomSong = songs[Math.floor(Math.random() * songs.length)];
            this.initializeAddToPlaylistForm();
        } while (randomSong === this.currentSong && songs.length > 1);

        return randomSong;
    }

    getNextSong() {
        if (this.isRandom) {
            return this.getRandomSong();
        }

        const songs = [...this.currentPlaylist.querySelectorAll('.song')];
        const currentIndex = songs.findIndex(song => song === this.currentSong);
        return songs[currentIndex + 1] || (this.isLooping ? songs[0] : null);
    }

    getPreviousSong() {
        const songs = [...this.currentPlaylist.querySelectorAll('.song')];
        const currentIndex = songs.findIndex(song => song === this.currentSong);
        return songs[currentIndex - 1] || (this.isLooping ? songs[songs.length - 1] : null);
    }

    play(songElement, playlistElement) {
        const songSrc = songElement.dataset.src;
        

        if (this.currentSong && this.currentSong !== songElement) {
            this.currentSong.querySelector('.play-button').classList.remove('active');
            this.currentSong.classList.remove('active');
        }

        if (this.audio.src !== songSrc) {
            this.audio.src = songSrc;
        }
        this.audio.play();
        this.isPlaying = true;
        this.currentSong = songElement;
        this.initializeAddToPlaylistForm();
        this.currentPlaylist = playlistElement;

        this.updatePlayerInfo(songElement);
        this.syncButtons(songElement);

        this.getSongTags();
    }

    pause(songElement) {
        this.audio.pause();
        this.isPlaying = false;
        songElement.querySelector('.play-button').classList.remove('active');
        songElement.classList.remove('active');
        this.playPauseButton.classList.remove('active');
        this.barPlayer.classList.remove('active');
    }

    togglePlayPause() {
        if (this.isPlaying) {
            this.pause(this.currentSong);
        } else {
            this.audio.play();
            this.isPlaying = true;
            this.currentSong.querySelector('.play-button').classList.add('active');
            this.currentSong.classList.add('active');
            this.playPauseButton.classList.add('active');
            this.barPlayer.classList.add('active');
        }
    }

    toggleLoop() {
        this.isLooping = !this.isLooping;

        if (this.isLooping) {
            this.loopButton.classList.add('active');
        } else {
            this.loopButton.classList.remove('active');
        }
    }

    next() {
        const nextSong = this.getNextSong();
        if (nextSong) {
            this.play(nextSong, this.currentPlaylist);
        }
    }

    previous() {
        if (this.audio.currentTime > 2) {
            this.audio.currentTime = 0;
        } else {
            const previousSong = this.getPreviousSong();
            if (previousSong) {
                this.play(previousSong, this.currentPlaylist);
            }
        }
    }

    handleSongEnd() {
        const nextSong = this.getNextSong();
        if (nextSong) {
            this.play(nextSong, this.currentPlaylist);
        } else {
            this.isPlaying = false;
            this.audio.currentTime = 0;
            this.syncButtons(null);
        }
    }

    updatePlayerInfo(songElement) {
        this.titleElement.textContent = songElement.querySelector('.title').textContent;
        this.artistElement.textContent = songElement.querySelector('.artist').textContent;

        const genres = [...songElement.querySelectorAll('.genres li')].map(li => li.textContent);
        this.genresElement.textContent = genres.join(', ');

        const thumbSrc = songElement.querySelector('.thumb').src;
        this.thumbElement.src = thumbSrc;
    }

    syncButtons(songElement) {
        document.querySelectorAll('.play-button').forEach(button => button.classList.remove('active'));
        document.querySelectorAll('.song').forEach(song => song.classList.remove('active'));

        if (songElement) {
            songElement.querySelector('.play-button').classList.add('active');
            songElement.classList.add('active');
            this.playPauseButton.classList.add('active');
            this.barPlayer.classList.add('active');
        } else {
            this.playPauseButton.classList.remove('active');
            this.barPlayer.classList.remove('active');
        }
    }

    updateProgressBar() {
        const progress = (this.audio.currentTime / this.audio.duration) * 100;
        this.progressBar.style.width = `${progress}%`;
    }

    seek(event) {
        const rect = this.progressBarContainer.getBoundingClientRect();
        const offsetX = event.clientX - rect.left;
        const percentage = offsetX / rect.width;
        this.audio.currentTime = percentage * this.audio.duration;
    }

    startDrag(event) {
        const dragHandler = (e) => this.seek(e);
        const stopDrag = () => {
            document.removeEventListener('mousemove', dragHandler);
            document.removeEventListener('mouseup', stopDrag);
        };

        document.addEventListener('mousemove', dragHandler);
        document.addEventListener('mouseup', stopDrag);

        this.seek(event);
    }
}

// Start player
const player = new Player();

//Take all playlists
document.querySelectorAll('.playlist').forEach(playlist => {
    const songs = playlist.querySelectorAll('.song');

    //start play the first song 
    songs.forEach(song => {
        const playButton = song.querySelector('.play-button');
        playButton.addEventListener('click', () => {
            if (player.currentSong === song && player.isPlaying) {
                player.pause(song);
            } else {
                player.play(song, playlist);
                player.initializeAddToPlaylistForm();
            }
        });
    });
});