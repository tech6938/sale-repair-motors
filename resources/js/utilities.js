window.isAsyncFormProcessing = false;

window.notify = function (message = "", status = "error", isToast = true) {
    status = status == "error" ? status : "success";

    if (message == "")
        message = status == "error"
            ? "Something went wrong."
            : "The action was successful.";

    let options = isToast
        ? {
            icon: status,
            title: message,
            showConfirmButton: false,
            confirmButtonColor: "#6576ff",
            toast: true,
            position: "bottom-end",
            timer: 5000,
            timerProgressBar: true,
            grow: "fullscreen",
        }
        : {
            icon: status,
            title: message,
            position: "center",
        };

    window.swal.fire(options);
}

window.handleAxiosError = function (error, form = null) {
    if (error?.response === undefined) return notify(error, "error", false);

    let response = error.response;

    if (typeof response.data == "string") {
        notify(
            response.data.replace(/<[^>]*>?/gm, ""),
            "error",
            false
        );
    } else if (
        typeof response.data.errors !== "undefined" && form !== null
    ) {
        for (let key in response.data.errors) {
            let target = form.find(`[name="${key}"]`);

            if (
                target.siblings(".invalid-feedback").length > 0
            ) {
                target.addClass("is-invalid")
                    .siblings(".invalid-feedback").text(response.data.errors[key][0]).show();
            } else if (
                target.closest(".form-group").find(".invalid-feedback").length > 0
            ) {
                target.addClass("is-invalid")
                    .closest(".form-group").find(".invalid-feedback").text(response.data.errors[key][0]).show();
            } else {
                notify(response.data.errors[key][0], "error", false);
            }
        }
    } else if (typeof response.data.message !== "undefined") {
        notify(
            response.data.message.replace(/<[^>]*>?/gm, ""),
            "error",
            false
        );
    } else {
        notify(
            "Oops! it seems like something is not right.",
            "error",
            false
        );
    }
}

$(function () {
    $("a.dark-switch").on("click", function () {
        window.axios({
            url: $(this).data("url"),
            method: "post",
            data: {
                is_dark_mode: $(this).data("value"),
            },
        });
    });

    $("a#toggle-sidebar").on("click", function () {
        window.axios({
            url: $(this).data("url"),
            method: "post",
            data: {
                is_compact_sidebar: $(this).data("value"),
            },
        });
    });

    $('.select2').select2();

    setTimeout(() => {
        $('.alert-auto-hide').slideUp(300);
    }, 4000);

    $.fn.dataTable.ext.errMode = "none";

    $(".dataTable").on("error.dt", function () {
        notify("Unauthenticated.", "error");
    });

    $(document).on("mouseenter", ".swal2-container", function () {
        window.swal.stopTimer();
    });

    $(document).on("mouseleave", ".swal2-container", function () {
        window.swal.resumeTimer();
    });

    $(document).on("init.dt", function (e, settings) {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
});
