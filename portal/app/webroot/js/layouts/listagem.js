const changeStatusTriggers = Array.from(
  document.querySelectorAll(".change-status")
);
async function changeStatus(a, target) {
  const toggleDisabled = () => {
    const parentNode = a.parentNode;
    [a, parentNode].forEach((el) => {
      el.classList.toggle("disabled");
    });
  };
  const { href = null } = a;
  if (!href) {
    return;
  }
  const icons = {
    active: "badge-success",
    inactive: "badge-important",
  };
  function resetStatus() {
    for (const icon of Object.values(icons)) {
      if (target.classList.contains(icon)) {
        target.classList.remove(icon);
      }
    }
  }
  try {
    toggleDisabled();
    const response = await fetch(href);
    const newStatus = await response.json();
    resetStatus();
    target.classList.add(newStatus == false ? icons.inactive : icons.active);
    toggleDisabled();
  } catch (e) {
    if (!!console.error) {
      console.error(e);
    }
  }
}
changeStatusTriggers.map((el) => {
  el.addEventListener("click", function (e) {
    e.preventDefault();
    changeStatus(this, this.parentNode.querySelector("span"));
  });
});