$(document).on("click", "a[delete-btn]", function (e) {
    e.preventDefault();

    const _self = $(this);
    const url = _self.attr("href");
    const dt = _self.data("datatable");
    const eventName = _self.data("event");

    window.swal
        .fire({
            title: "Are you sure?",
            text: "You want to delete this record.",
            icon: "error",
            showCancelButton: true,
            confirmButtonColor: "#e85347",
            confirmButtonText: "Yes, delete it!",
            cancelButtonColor: "#ebeef2",
            customClass: {
                cancelButton: 'sweet-alert-cancel-btn',
            }
        })
        .then((result) => {
            if (!result.value) return;

            window.swal.fire({
                title: "",
                text: "Please wait...",
                showConfirmButton: false,
                backdrop: true,
            });

            window.axios
                .delete(url)
                .then((response) => {
                    if (dt !== "") $(dt).DataTable().ajax.reload();

                    if (eventName) {
                        $(document).trigger(eventName, response.data?.params);
                    }

                    if (typeof response.data.message !== "undefined") {
                        notify(response.data.message, "success");
                    } else {
                        notify(response.data, "success");
                    }
                })
                .catch((error) => {
                    window.swal.close();
                    handleAxiosError(error, "error");
                });
        });
});

$(document).on("click", "a[confirm-btn]", function (e) {
    e.preventDefault();

    let _self = $(this);
    let dt = _self.data("datatable");

    window.swal
        .fire({
            title: _self.data("title") ?? "Are you sure?",
            text: _self.data("message") ?? "Do you really want to do this action?",
            icon: _self.data("icon") ?? "warning",
            showCancelButton: true,
            confirmButtonText: _self.data("confirm-btn-text") ?? "Yes, do it!",
            confirmButtonColor: _self.data("confirm-btn-color") ?? "#041e42",
            cancelButtonText: _self.data("cancel-btn-text") ?? "Cancel",
            cancelButtonColor: "#ebeef2",
            customClass: {
                cancelButton: 'sweet-alert-cancel-btn',
            }
        })
        .then((result) => {
            if (!result.value) return;

            window.swal.fire({
                title: "",
                text: "Please wait...",
                showConfirmButton: false,
                backdrop: true,
            });

            window
                .axios({
                    url: _self.attr("href"),
                    method: _self.data("method") ?? "get",
                })
                .then((response) => {
                    if (dt !== "") $(dt).DataTable().ajax.reload();

                    if (typeof response.data.message !== "undefined") {
                        notify(response.data.message, "success");
                    } else {
                        notify(response.data, "success");
                    }
                })
                .catch((error) => {
                    window.swal.close();
                    handleAxiosError(error, _self);
                });
        });
});
