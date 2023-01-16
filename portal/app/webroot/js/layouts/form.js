const pageManager = {
  rowManager: {
    wrapper: null,
    lastIndex: 1,
    tables: [],
    attachListeners(context = null) {
      const onContainer = (container) => {
        const selects = Array.from(container.querySelectorAll("select"));
        selects.map((el) => $(el).select2());
        $(container.querySelector(".change-table")).on(
          "select2:select",
          function (e) {
            const { id } = e.params.data;
            const table = pageManager.rowManager.tables.find(
              (t) => t.original == id
            );
            const targetReflect = container.querySelector(
              ".reflect-table-rows"
            );
            targetReflect.innerHTML = "";
            for (const field of table.fields) {
              targetReflect.innerHTML += `<option value="${field}">${field.toString().toUpperCase()}</option>`;
            }

            $(targetReflect).select2();
          }
        );

        const deleteBtn = container.querySelector('.delete-row');
        if(!deleteBtn) {
          return;
        }
        deleteBtn.addEventListener("click", (e) => {
          e.preventDefault();
          container.remove();
        });
      };
      if (context) {
        onContainer(context);
        return;
      }
      const containers = Array.from(document.querySelectorAll(".content-row"));
      for (const container of containers) {
        onContainer(container);
      }
    },
    add: function () {
      const container = document.createElement("div");
      container.classList.add("row", "content-row");

      container.innerHTML += `
					<div class="input-field small">
						<label>Posição</label>
						<input class="centered" type="number" min="1" name="layout[columns][${
              this.lastIndex
            }][position]" />
					</div>

					<div class="input-field">
						<label>Tabela</label>
						<select class="centered change-table" name="layout[columns][${
              this.lastIndex
            }][tabela]">
              ${this.tables.map(
                (table) =>
                  `<option value="${table.original}">${table.name}</option>`
              )}
						</select>
					</div>
					<div class="input-field">
						<label>Coluna</label>
						<select class="centered reflect-table-rows" name="layout[columns][${
              this.lastIndex
            }][coluna]">
							${this.tables[0].fields.map(
                (field) => `<option value="${field}">${field.toString().toUpperCase()}</option>`
              )}
						</select>
					</div>



          <button class="delete-row"><icon class="icon-trash"></icon></button>
			`;

      this.lastIndex++;
      this.attachListeners(container);
      this.wrapper.appendChild(container);
    },

    load: function () {
      this.wrapper = document.getElementById("row-wrapper");
      const tablesJson = document.getElementById("full-tables-data")?.innerHTML;
      const tables = JSON.parse(tablesJson);
      this.tables = tables;
    },
  },
  form: {
    _target: null,
    triggers: {
      addRow: null,
    },
    configs: {
      timeout: 400,
    },
    show: function () {
      $(this._target).show(this.configs.timeout);
    },
    hide: function () {
      $(this._target).hide(this.configs.timeout);
    },
    load: function () {
      this._target = document.getElementById("store-page");
      this.triggers.addRow = document.getElementById("add-row__trigger");
      return this;
    },
    listen: function () {
      this.triggers.addRow.addEventListener("click", () =>
        pageManager.rowManager.add()
      );
    },
  },
  load: function () {
    this.rowManager.load();
    this.form.load();
    return this;
  },
  listen: function () {
    this.form.listen();
    this.rowManager.attachListeners();
  },
};
document.addEventListener("DOMContentLoaded", () => {
  $("select").select2();
  pageManager.load().listen();
});
