var host = $("meta[name='home_url']").attr("content");
var Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 5000
});

//Sell With DCM
$(document).on("submit", "#newsletterForm", function (event) {
    var form = $(this);
    var formData = new FormData($("#newsletterForm")[0]);
    $(".errors").css({ display: "none" });
    $(".subscribe-btn").html('<img src="'+host+'/public/loader-gif.gif" width="80px" class="newsletter-loader">');
    $.ajax({
        type: "POST",
        url: form.attr("action"),
        data: formData,
        dataType: "json",
        encode: true,
        processData: false,
        contentType: false,
    })
        .done(function (data) {
            if (data.success == "success") {
                Toast.fire({
                    icon: "success",
                    title: data.message,
                });

                /* conversion event for Google Tag Manager, sent before the reload */
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ event: 'newsletterSubscribeEvent' });

                setTimeout(function () {
                    location.reload();
                }, 1500);
            } else {
                Toast.fire({
                    icon: "warning",
                    title: data.message,
                });
            }
            $(".subscribe-btn").html('SUBSCRIBE');
        })
        .fail(function (e) {
            $(".subscribe-btn").html('SUBSCRIBE');
            /* `data` does not exist here (that was a ReferenceError that hid the real message): read it from the failed response */
            var res = e && e.responseJSON ? e.responseJSON : {};
            var msg = res.message || 'Something went wrong! Try again later.';
            if (res.errors) { var k = Object.keys(res.errors)[0]; if (k) msg = res.errors[k][0]; }
            Toast.fire({
                icon: "warning",
                title: msg,
            });
        });

    event.preventDefault();
});