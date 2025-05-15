window.initImagePicker = function ($targetElement) {
    let picker = $targetElement ?? $("[image-picker]"),
        imageElement = picker.find("img"),
        defaultImage = imageElement.attr("src"),
        initialsElement = picker.find(".user-avatar").find("span"),
        pickBtn = picker.find("a"),
        pickBtnDefaultText = picker.find("a").text(),
        submitBtn = picker.closest("form").find('[type="submit"]'),
        toggleSubmitBtn = picker.data("toggle-submit-btn") !== "undefined" && picker.data("toggle-submit-btn"),
        maxSize = typeof picker.data("max-size") !== undefined && picker.data("max-size") > 0
            ? picker.data("max-size")
            : 1000000; // 1000000 Bytes = 1 MB

    picker.on("change", "input", function () {
        const fileInput = $(this);

        if (this.files && this.files[0]) {
            if (this.files[0].size > maxSize) {
                notify(
                    `The selected image must be less then ${maxSize / 1000000} MB.`,
                    "error",
                    false
                );

                return;
            }

            let reader = new FileReader();

            reader.onload = function (e) {
                imageElement.attr("src", e.target.result).show();
            };

            reader.readAsDataURL(this.files[0]);

            pickBtn.text(fileInput.val().split("\\").pop());

            if (initialsElement.length > 0) initialsElement.hide();
            if (toggleSubmitBtn) submitBtn.slideDown();
        } else {
            if (initialsElement.length > 0 && defaultImage.length <= 0) {
                imageElement.hide();
                initialsElement.show();
            } else {
                imageElement.attr("src", defaultImage).show();
                initialsElement.hide();
            }

            pickBtn.text(pickBtnDefaultText);
            if (toggleSubmitBtn) submitBtn.slideUp();
        }
    });

    pickBtn.on("click", function () {
        picker.find("input").trigger("click");
    });
}