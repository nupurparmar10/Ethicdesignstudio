document.addEventListener("alpine:init", () => {
    Alpine.store("appStore", {
        dir: sessionStorage.getItem("dir") || "ltr",

        toggleDir() {
            console.log("toggleDir", this.dir);
            this.dir = this.dir === "ltr" ? "rtl" : "ltr";
            sessionStorage.setItem("dir", this.dir);

            this.handleUrl();
        },

        handleUrl() {
            var bootstrapLinkEle = document.getElementById("bootstrap-style");
            var appLinkEle = document.getElementById("app-style");

            // Get base URL: http://localhost/project-folder/
            var pathParts = window.location.pathname.split('/').filter(Boolean);
            var baseUrl = window.location.origin + '/' + pathParts[0] + '/';

                bootstrapLinkEle.setAttribute("href", baseUrl + "assets/css/bootstrap.min.css");
                appLinkEle.setAttribute("href", baseUrl + "assets/css/app.min.css");
        }
    });

    Alpine.store("appStore").handleUrl();
});