import "bootstrap";
import "bootstrap-input-spinner";
import "slick-carousel";
import "select2";
const feather = require("feather-icons");

require("./ajax");

const receipt_flag = $('meta[name="receipt"]').attr("content");

$(function() {
  let head_h = $("#header").outerHeight() + 16;
  let window_w = $(window).outerWidth();
  let telephone_no = '<meta name="format-detection" content="telephone=no">';

  // meta制御
  $("head meta:last").after(telephone_no);

  // featherIcons
  feather.replace({
    width: 18,
  });

  // OnlyTOP
  if (receipt_flag == "on") {
    $("#FirstSelect").modal(
      {
        backdrop: "static",
      },
      "show"
    );
  }
  $("#changeDateBtn .link").on("click", function() {
    $("#FirstSelect").modal(
      {
        backdrop: "static",
      },
      "show"
    );
  });
  $("#salestop").modal("show");

  // STEP1
  $("#step1")
    .find(".btn")
    .on("click", function() {
      $("#set-service").val($(this).attr("name"));
      $("#step1").removeClass("show active");
      $("#step2").addClass("show active");
      $("#first-progress .steps").text("2/3");
      $("#first-progress .progress-bar").css("width", "33.33333%");
      $("#first-progress .progress-bar").attr("aria-valuenow", "33.33333");

      vali_select_shop();
    });
  // STEP2
  $("#step2")
    .find(".btn")
    .on("click", function() {
      $("#step2").removeClass("show active");
      $("#step3").addClass("show active");
      $("#first-progress .steps").text("3/3");
      $("#first-progress .progress-bar").css("width", "66.66666%");
      $("#first-progress .progress-bar").attr("aria-valuenow", "66.66666%");
    });

  // スムーススクロール
  $(".smooth").on("click", function() {
    let speed = 500;
    let href = $(this).attr("href");
    let target = $(href == "#" || href == "" ? "html" : href);
    let position = target.offset().top - head_h;
    $("html, body").animate({ scrollTop: position }, speed, "swing");
    return false;
  });

  // SPメニュー
  $("#spopen, .overray, .spmenu-close").on("click", function() {
    $(this).toggleClass("active");
    $("#spmenu").toggleClass("active");
    $("body").toggleClass("spopen");
  });

  // bootstrap-input-spinner
  $(".num-spinner").inputSpinner();

  // slick
  $(".home-slide").slick({
    dots: true,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    centerMode: true,
    centerPadding: "10vw",
    autoplay: true,
    autoplaySpeed: 5000,
    arrows: false,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1,
          centerMode: false,
          centerPadding: "0",
        },
      },
    ],
  });
  $(".modal-slide").slick({
    dots: true,
    infinite: true,
    speed: 300,
    autoplay: false,
    arrows: false,
  });

  // carousel
  $(".carousel").carousel({
    interval: false,
  });

  // --- home ------------------------------------ //
  if (window_w >= 990) {
    $("#catalog").addClass("container");
  }
  $(".addcart").on("click", function() {
    $(".modal").modal("hide");
  });
  $("#deliveryShop, #changeDeliveryShop").select2({
    theme: "bootstrap4",
    placeholder: "店舗を選択",
  });

  // STEP
  $("#deliveryTime").on("change", function() {
    if ($(this).children().length == $(this).prop("selectedIndex") + 1) {
      $("#deliverySeconds").val("00");
      $("#deliverySeconds").attr("disabled", true);
    } else {
      $("#deliverySeconds").attr("disabled", false);
    }
  });

  // --- cart ------------------------------------ //
  $(".okimochi-btns .btn").on("click", function() {
    $(".okimochi-btns .btn").removeClass("active");
    $(this).addClass("active");
    $("input#okimochi").val($(this).attr("data-price"));

    $(".js-okimochi-amount").text(Number($(this).attr("data-price")).toLocaleString());
    $(".js-cart-total").text((Number($(".js-cart-amount").attr("data-amount")) + Number($(this).attr("data-price"))).toLocaleString());
  });
  $(".btn-cartdelete").on("click", function() {
    $('#cartdelete input[name="product_id"]').val($(this).attr("data-id"));
    $("#cartdelete").submit();
  });

  $("#width-login").on("click", function() {
    $("#cartform .seconds").append('<input type="hidden" name="email" value="' + $("#login_email").val() + '" />');
    $("#cartform .seconds").append('<input type="hidden" name="password" value="' + $("#login_password").val() + '" />');
    $("#cartform").submit();
  });

  // --- form ------------------------------------ //
  $("#zipcode, #address1, #address2").on("change", function() {
    const zip = ["中央区", "港区", "千代田区", "品川区", "目黒区", "渋谷区"];
    let val = $("#address1").val();
    if (zip.includes(val)) {
      $("#alert-address").css("display", "none");
      $('button[type="submit"]').removeAttr("disabled");
    } else {
      $("#alert-address").css("display", "block");
      $('button[type="submit"]').attr("disabled", true);
    }
  });
  // 支払い方法
  if ($("select#pay").val() == 0 && $("select#pay").val() != "") {
    $("#payjp_checkout_box").css("display", "block");
  } else {
    $("#payjp_checkout_box").css("display", "none");
  }
  $("select#pay").on("change", function() {
    if ($(this).val() == 0 && $(this).val() != "") {
      $("#payjp_checkout_box").css("display", "block");
    } else {
      $("#payjp_checkout_box").css("display", "none");
    }
  });

  // クーポン
  $("#coupon").on("change", function() {
    if ($(this).val() != "") {
      $("#couponSuccess").css("display", "block");
    } else {
      $("#couponSuccess").css("display", "none");
    }
  });
});

function vali_select_shop() {
  if ($("#FirstSelect #deliveryShop").val() == "") {
    $("#next-step3").attr("disabled", true);
  } else {
    $("#next-step3").attr("disabled", false);
  }
}
