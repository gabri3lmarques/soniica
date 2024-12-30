<div class="sidebar">
<!-- Formulário para Criar Playlist -->
<form id="create-playlist-form">
  <label for="playlist_name">Nome da Playlist:</label>
  <input type="text" id="playlist_name" name="playlist_name" placeholder="Digite o nome da playlist" required>
  <button type="button" id="create-playlist-btn">Criar Playlist</button>
</form>

<!-- Mensagem de Feedback -->
<div id="playlist-feedback" style="margin-top: 10px;"></div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const createPlaylistButton = document.getElementById("create-playlist-btn");
    const feedbackDiv = document.getElementById("playlist-feedback");

    createPlaylistButton.addEventListener("click", async () => {
      const playlistNameInput = document.getElementById("playlist_name");
      const playlistName = playlistNameInput.value.trim();

      if (!playlistName) {
        feedbackDiv.textContent = "O nome da playlist não pode estar vazio.";
        feedbackDiv.style.color = "red";
        return;
      }

      // Criar a playlist
      try {
        await createPlaylist(playlistName);
        feedbackDiv.textContent = `Playlist "${playlistName}" criada com sucesso!`;
        feedbackDiv.style.color = "green";
        playlistNameInput.value = ""; // Limpa o campo de entrada
      } catch (error) {
        feedbackDiv.textContent = "Erro ao criar a playlist.";
        feedbackDiv.style.color = "red";
        console.error("Erro ao criar a playlist:", error);
      }
    });
  });
</script>

</div>
