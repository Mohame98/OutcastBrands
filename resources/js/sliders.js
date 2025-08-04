// $(".media-input.brand").slick({
//   fade: true,
//   infinite: false,
//   slidesToShow: 1,
//   slidesToScroll: 1,
//   arrows: true,
// });

// function initBrandMediaPreview() {
//   $(".media-preview.brand").slick({
//     fade: true,
//     infinite: false,
//     slidesToShow: 1,
//     slidesToScroll: 1,
//   });
// }

// export {
//   initBrandMediaPreview,
// };

function initBrandMediaPreview() {
  $(".media-preview.brand").slick({
    fade: true,
    infinite: false,
    slidesToShow: 1,
    slidesToScroll: 1,
  });
}

function initBrandImgSlider() {
  $(".brand-image-slider").slick({
    fade: true,
    infinite: false,
    slidesToShow: 1,
    slidesToScroll: 1,
  });
}

export {
  initBrandImgSlider,
  initBrandMediaPreview,
};