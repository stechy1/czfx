var m = new SettingsManager();

jQuery(document).ready(function () {
    jQuery(".form-control").keyup(function (e) {
        if (e.keyCode == 13) {
            var name = $(this).attr("name");
            var val  = $(this).val();

            m.update(name, val);
        }
    });

    jQuery(".set-as-default").click(function () {
        var formData = new FormData();
        jQuery(".config-item").each(function () {
            var self = $(this);
            var key = self.attr("name");
            var val = self.val();
            formData.append(key, val);
        });

        m.setAsDefault(formData);
    });
});