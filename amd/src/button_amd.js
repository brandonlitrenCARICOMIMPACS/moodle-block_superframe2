export const button = (text) => {
  const button = document.createElement("button");
  button.innerHTML = text;

  const body = document.getElementById("button");
  body.appendChild(button);

  button.addEventListener("click", function () {
    alert("Hello I guess");
  });
};
