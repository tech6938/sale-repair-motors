$(document).on("submit", "form[async-form]", function (e) {
    e.preventDefault();

    const form = this;
    const confirm = $(form).data("confirm");
    const confirmMessage = $(form).data("confirm-message");

    if (confirm == "yes") {
        window.swal
            .fire({
                title: "Are you sure?",
                text: confirmMessage ?? "Do you really want to submit this form?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#e7e7e7",
                confirmButtonText: "Yes, do it!",
            })
            .then((result) => {
                if (result.value) sendAsyncForm(form);
            });
    } else {
        sendAsyncForm(form);
    }
});

function sendAsyncForm(form) {
    const _self = $(form);
    const btn = _self.find("[type=submit]");
    const btnHtml = btn.html();
    const onAsyncModal = _self.attr("on-async-modal");
    const resetForm = _self.data("reset-form");
    const dt = _self.data("datatable");
    const eventName = _self.data("event");
    const disableToast = _self.data("disable-toast");

    _self
        .find("input")
        .removeClass("is-invalid")
        .siblings(".invalid-feedback")
        .text("")
        .hide();

    btn.attr("disabled", "disabled");
    btn.html(
        `<span class="spinner-border spinner-border-sm"></span>&nbsp;&nbsp;<span>${btnHtml}</span>`
    );

    isAsyncFormProcessing = true;

    window
        .axios({
            url: _self.attr("action"),
            method: _self.attr("method"),
            data: new FormData(_self[0]),
        })
        .then((response) => {
            if (onAsyncModal !== undefined) $('#async-modal').modal("hide");
            if (resetForm !== undefined) _self.trigger("reset");
            if (dt !== "") $(dt).DataTable().ajax.reload();

            if (eventName) {
                $(document).trigger(eventName, response.data?.params);
            }

            if (disableToast) return;

            if (typeof response.data.message !== "undefined") {
                notify(response.data.message, "success");
            } else {
                notify(response.data, "success");
            }
        })
        .catch((error) => {
            handleAxiosError(error, _self);
        })
        .finally(() => {
            btn.removeAttr("disabled").html(btnHtml);
            isAsyncFormProcessing = false;
        });
}