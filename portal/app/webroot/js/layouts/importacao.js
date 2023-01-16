const page = {
  modal: {
    container: null,
    closeBtn: null,
    openBtn: null,
    uploadLabel: null,
    uploadFalse: null,
    uploadReal: null,
    toggle(hide = false) {
      this.container.style.display = hide ? "none" : "block";
      this.container.style.zIndex = hide ? "-1" : "1050";
      $(this.container).modal(hide ? "hide" : "show");
    },
    onSubmit(form) {
      if (!form) {
        return;
      }
      const url = form.action;
      const data = new FormData(form);
      const blockContainer = $(".btn-success").parent();
      bloquearDiv(blockContainer);
      $.ajax({
        url,
        type: "POST",
        data,
        processData: false,
        contentType: false,
        success: function (response) {
          page.modal.toggle(true);
          const data = JSON.parse(response);
          desbloquearDiv(blockContainer);
          if (data?.errors) {
            flashMessage(data.errors[0], "error");
            return;
          }
          flashMessage("Upload realizado com sucesso", "success");
          atualizaListaLayouts();
        },
        error() {
          desbloquearDiv(blockContainer);
        },
      });
    },
    load() {
      this.container = document.getElementById("upload-modal");
      this.closeBtn = this.container.querySelector(".close-modal");
      this.openBtn = document.getElementById("open-save-modal");
      this.uploadFalse = document.getElementById("upload-trigger");
      this.uploadReal = document.getElementById("upload-real");
      this.uploadLabel = document.querySelector("label[for='upload-trigger']");
    },
    listen() {
      this.closeBtn.addEventListener("click", (e) => {
        e.preventDefault();
        this.toggle(true);
      });
      this.openBtn.addEventListener("click", () => this.toggle());
      this.container.addEventListener("submit", function (e) {
        e.preventDefault();
        page.modal.onSubmit(this.querySelector("form"));
      });
      this.uploadFalse.addEventListener("click", (e) => {
        e.preventDefault();
        this.uploadReal.click();
      });
      this.uploadReal.addEventListener("change", () => {
        this.uploadLabel.innerHTML =
          this.uploadReal.files[0].name ?? "Nenhum arquivo selecionado";
      });
    },
  },
  load() {
    this.modal.load();
    return this;
  },
  listen() {
    this.modal.listen();
  },
};

page.load().listen();
