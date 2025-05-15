$(document).on("click", "a[async-modal]", function (e) {
    e.preventDefault();

    const _self = $(this);
    const asyncModal = $("#async-modal");

    const content = asyncModal.find("#content");
    const spinner = asyncModal.find("#spinner");

    content.hide();
    spinner.show();

    let modalSize = "modal-lg";

    switch (_self.attr("async-modal-size")) {
        case "sm":
            modalSize = "modal-sm";
            break;
        case "md":
            modalSize = "modal-lg";
            break;
        case "lg":
            modalSize = "modal-xl";
            break;
    }

    asyncModal
        .find(".modal-dialog")
        .removeClass("modal-xs modal-sm modal-lg modal-xl")
        .addClass(modalSize);

    if (_self.data("classes")) {
        asyncModal
            .find(".modal-body")
            .addClass(_self.data("classes"))
            .data("classes", _self.data("classes"));
    }

    asyncModal.modal({ backdrop: 'static', keyboard: false });
    asyncModal.modal('show');

    window
        .axios({
            method: _self.data("method") ?? "get",
            url: _self.attr("href"),
        })
        .then((response) => {
            spinner.hide();

            if (response.status === 200) content.html(response.data).show();
            else notify();

            $(document).trigger("asyncmodal.loaded");
        })
        .catch((error) => {
            handleAxiosError(error);
        })
        .finally(() => {
            spinner.hide();
        });
});

$(document).on("click", "[async-modal-close]", function () {
    if (!isAsyncFormProcessing) $("#async-modal").modal("hide");
});

$(document).on("asyncmodal.loaded", function () {
    let asyncmodal = $("#async-modal");

    asyncmodal.on("change", ".custom-file-input", function () {
        $(this)
            .next(".custom-file-label")
            .html(
                $(this).val() ? $(this).val().split("\\").pop() : "Choose a file"
            );
    });

    if (asyncmodal.find("[image-picker]").length > 0) {
        initImagePicker($(asyncmodal.find("[image-picker]")));
    }

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
});