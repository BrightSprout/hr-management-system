(function() {
  function passwordGenerator(length) {
    let password = "";
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    for (let i = 0; i < length;i++)
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    return password;
  }

 
  for (let btn of document.querySelectorAll(".toggle-view-password")) {
    btn.addEventListener("click", function() {
      const isHidden =  this.previousElementSibling.type == "password";
      this.previousElementSibling.type = isHidden ? "text" : "password"; 
      this.children[0].setAttribute("data-lucide", isHidden  ? "eye" : "eye-off");
      lucide.createIcons();
    });
  }

  document.querySelector("#generate-new-password").addEventListener("click", function() {
    const newPassword = passwordGenerator(12); 
    document.querySelector("input[name='password']").value = newPassword;
    document.querySelector("input[name='confirm_password']").value = newPassword;
  });

  document.querySelector("#reset-password-form").addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const password = formData.get("password");
    if (password != formData.get("confirm_password"))
      return Swal.fire({
        icon: "error",
        title: "Incorrect Confirm Password",
        text: "Password and Confirm Password did not match",
      });
    if (!/^\w+$/.test(password))
      return Swal.fire({
        icon: "error",
        title: "Invalid Password",
        text: "Password only allows letters and numbers",
      });
    const response = await fetch("api/reset-password", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({user_id: formData.get("id"), new_password: formData.get("password")})
    });
    if (!response.ok)
      return Swal.fire({
        icon: "error",
        title: "Reset Password Failed",
        text: "Something went wrong...",
      });
     await Swal.fire({
       icon: "success",
       title: "Password Reset",
       text: "Successfully reset password...",
     });
    this.reset();
  });
})();
