// Playlist API and JavaScript Interaction for Soniica

// Utilize o objeto 'soniicaApi' passado pelo wp_localize_script
const apiBase = soniicaApi.apiBase;
const nonce = soniicaApi.nonce;

// Utility: Make API Request
// Variável para rastrear requisições em andamento
let ongoingRequests = {};

const apiRequest = async (method, endpoint = "", body = null) => {
  const requestKey = `${method}:${endpoint}:${JSON.stringify(body)}`;

  // Evitar requisições duplicadas
  if (ongoingRequests[requestKey]) {
    console.warn("Requisição duplicada detectada:", requestKey);
    return Promise.resolve({ error: "Requisição duplicada bloqueada." });
  }

  ongoingRequests[requestKey] = true; // Marca a requisição como em andamento

  try {
    const response = await fetch(`${apiBase}/${endpoint}`, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': nonce, // Segurança com o nonce
      },
      body: body ? JSON.stringify(body) : null,
    });

    return await response.json();
  } catch (error) {
    console.error("Erro na requisição:", error);
    throw error;
  } finally {
    delete ongoingRequests[requestKey]; // Remove a requisição ao finalizar
  }
};

// Function to Create Playlist
const createPlaylist = async (playlistName) => {
  const result = await apiRequest("POST", "", { name: playlistName });
  alert(result.message || "Playlist created!");
};

// Function to Delete Playlist
const deletePlaylist = async (playlistId) => {
  const result = await apiRequest("DELETE", playlistId);
  alert(result.message || "Playlist deleted!");
};

// Function to Edit Playlist Name
const editPlaylistName = async (playlistId, newName) => {
  const result = await apiRequest("PATCH", playlistId, { name: newName });
  alert(result.message || "Playlist updated!");
};

// Function to Add Song to Playlist
const addSongToPlaylist = async (playlistId, songId) => {
  const result = await apiRequest("POST", `${playlistId}/add-song`, { song_id: songId });
  alert(result.message || "Song added to playlist!");
};

// Function to Remove Song from Playlist
const removeSongFromPlaylist = async (playlistId, songId, button) => {
  if (!confirm("Tem certeza que deseja remover essa música da playlist?")) {
    return;
  }

  try {
      const response = await apiRequest("DELETE", `${playlistId}/remove-song`, { song_id: songId });

      // Certifique-se de que a resposta é interpretada corretamente
      const result = response;

      let message;
      if (result.message) {
          message = result.message; // Usa a mensagem retornada pela API
      } else {
          message = "Música removida da playlist."; // Mensagem padrão
      }
      
      alert(message);
      
      // Remover a música do front-end após sucesso
      button.closest("li").remove();
  } catch (error) {
      console.error("Erro ao remover a música:", error);
      alert("Ocorreu um erro ao remover a música.");
  }
};


