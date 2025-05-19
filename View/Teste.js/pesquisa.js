//busca.html

document.getElementById('busca').addEventListener('input', function () {
  const termo = this.value.toLowerCase();
  const itens = document.querySelectorAll('.jogos-grid .jogos');

  itens.forEach(function (item) {
    const textoItem = item.textContent.toLowerCase();
    if (textoItem.includes(termo)) {
      item.style.display = '';
    } else {
      item.style.display = 'none';
    }
  });
});