// efeito hover no link de login
const loginLink = document.querySelector('.login_link');
const userIcon = document.querySelector('.user-icon');

// Adiciona os eventos de mouseover e mouseout ao elemento com a classe 'login_link'
loginLink.addEventListener('mouseover', () => {
    userIcon.classList.add('active'); 
});

loginLink.addEventListener('mouseout', () => {
    userIcon.classList.remove('active'); 
});

//efeito de foco no search 
const input = document.getElementById('s');
const searchForm = document.querySelector('.searchform');

// Adiciona evento de foco no input
input.addEventListener('focus', () => {
    searchForm.classList.add('active'); // Adiciona a classe 'active'
});

// Adiciona evento de perda de foco no input
input.addEventListener('blur', () => {
    searchForm.classList.remove('active'); // Remove a classe 'active'
});

