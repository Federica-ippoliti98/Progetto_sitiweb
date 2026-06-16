const panels = document.querySelectorAll('.panel');

panels.forEach(panel => {

  // CLICK (para móvil y escritorio)
  panel.addEventListener('click', () => {
    removeActiveClasses();
    panel.classList.add('active');
  });

  // Cuando el mouse sale, quitar active si no fue clic
  panel.addEventListener('mouseleave', () => {
    if (!panel.classList.contains('clicked')) {
      panel.classList.remove('active');
    }
  });

});

function removeActiveClasses() {
  panels.forEach(panel => {
    panel.classList.remove('active');
  });
}