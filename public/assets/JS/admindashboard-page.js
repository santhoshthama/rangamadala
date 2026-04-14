function closeToast() {
  const toast = document.getElementById('successToast');
  if (toast) {
    toast.style.animation = 'toastSlideOut 0.4s ease forwards';
    setTimeout(() => toast.remove(), 400);
  }
}

window.addEventListener('load', function() {
  const toast = document.getElementById('successToast');
  if (toast) {
    setTimeout(() => {
      closeToast();
    }, 4000);
  }
});
