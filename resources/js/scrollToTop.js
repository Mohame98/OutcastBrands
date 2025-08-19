function scrollToTop() {
  const scrollToTopBtn = document.querySelector("#scrollToTopBtn");
  window.onscroll = function() {
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
      scrollToTopBtn.style.display = "block"; 
    } else {
      scrollToTopBtn.style.display = "none";
    }
  };

  scrollToTopBtn.addEventListener("click", function(){
    window.scrollTo({
      top: 0,
      left: 0,
      behavior: 'smooth'
    });
  })
}

export { scrollToTop };