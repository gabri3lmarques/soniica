
document.querySelectorAll('.category-menu .category-menu_header').forEach(header => {
  header.addEventListener('click', () => {
    const category = header.parentElement;

    // Verifica se a categoria já está ativa
    const isActive = category.classList.contains('active');

    // Fecha todas as categorias
    document.querySelectorAll('.category-menu').forEach(cat => {
      cat.classList.remove('active');
    });

    // Se a categoria não estava ativa, abre a categoria clicada
    if (!isActive) {
      category.classList.add('active');
    }
  });
});

