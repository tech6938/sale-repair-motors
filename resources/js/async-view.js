$(document).on("click", "a[async-view]", function (e) {
    e.preventDefault();

    const _self = $(this);
    const container = $(_self.data("container") ?? "#async-view-container");

    let spinnerTimeout = null;

    const spinner = $(document).find("#fullscreen-spinner");

    // Show spinner only if request takes more than 200ms
    spinnerTimeout = setTimeout(() => {
        spinner.css('display', 'flex');
    }, 200);

    _self.closest('ul').find('li a').removeClass('active')
    _self.addClass('active');

    window
        .axios({
            method: _self.data("method") ?? "get",
            url: _self.attr("href"),
        })
        .then((response) => {
            if (response.status === 200) container.html(response.data);
            else notify();

            $(document).trigger("async-view.loaded");
        })
        .catch((error) => {
            handleAxiosError(error);
        })
        .finally(() => {
            // Cancel showing spinner if not shown yet
            clearTimeout(spinnerTimeout);

            // Hide spinner if it was shown
            spinner.hide();
        });
});

$(document).on("async-view.loaded", function () {
    $('.select2').select2();

    $('[data-bs-toggle="tooltip"]').tooltip();

    NioApp.Passcode(".passcode-switch");

    NioApp.Validate('.form-validate', {
        errorElement: "span",
        errorClass: "invalid",
        errorPlacement: function errorPlacement(error, element) {
            if (element.parents().hasClass('input-group')) {
                error.appendTo(element.parent().parent());
            } else {
                error.appendTo(element.parent());
            }
        }
    });

    NioApp.Lightbox('.popup-image', 'image');
});