export default {
  fadeIn(callback) {
    const el = document.querySelector('#overlay');
    if (el) {
      el.classList.add('overlay--active');
      if (callback) {
        el.addEventListener('click', callback);
      }
    }
  },
  fadeOut(callback) {
    const el = document.querySelector('#overlay');
    if (el) {
      el.classList.remove('overlay--active');
      if (callback) {
        el.removeEventListener('click', callback);
      }
    }
  }
};
