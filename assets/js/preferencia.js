const toggleImage = document.getElementById('theme-toggle-image');

// Verifique se há um tema armazenado no Local Storage e defina-o ao carregar a página
if (localStorage.getItem('theme') === 'dark') {
  document.body.classList.add('dark-mode');
}

// Adicione um ouvinte de evento ao botão para alternar o tema
toggleImage.addEventListener('click', function() {
  // Verifique se o tema atual é claro ou escuro e alterne-o
  if (document.body.classList.contains('dark-mode')) {
    document.body.classList.remove('dark-mode');
    localStorage.setItem('theme', 'light'); // Armazene o tema claro no Local Storage
  } else {
    document.body.classList.add('dark-mode');
    localStorage.setItem('theme', 'dark'); // Armazene o tema escuro no Local Storage
  }
});