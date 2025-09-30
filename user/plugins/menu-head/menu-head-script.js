// (script simples — se quiser dropdowns etc, adapte)
document.addEventListener('DOMContentLoaded', function(){
  // Exemplo: marca o item ativo baseado na URL
  const items = document.querySelectorAll('.mh-menu-item a');
  const current = window.location.pathname;
  items.forEach(a => {
    if ( a.getAttribute('href') === current ) {
      a.classList.add('mh-active');
    }
  });
});
