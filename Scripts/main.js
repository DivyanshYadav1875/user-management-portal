document.addEventListener('DOMContentLoaded', () => {
  const loginBtn = document.getElementById("loginBtn");
  const registerBtn = document.getElementById("registerBtn");

  // LOGIN
  loginBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const name = document.getElementById("loginName").value.trim();
    const password = document.getElementById("loginPassword").value.trim();

    if (!name || !password) return alert("Please fill all login fields");

    try {
      const response = await fetch("Scripts/submit-action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "login", name, password })
      });

      const text = await response.text();
      console.log("Login response:", text);

      const result = JSON.parse(text);

      if (result.success) {
        alert("Login successful!");
        window.location.href = "dashboard.html";
      } else {
        alert(result.message || "Login failed");
      }

    } catch (err) {
      console.error("Login error:", err);
      alert("Unexpected error during login.");
    }
  });

  // REGISTRATION
  registerBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const name = document.getElementById("regName").value.trim();
    const email = document.getElementById("regEmail").value.trim();
    const password = document.getElementById("regPassword").value.trim();

    if (!name || !email || !password) return alert("Please fill all registration fields");

    try {
      const response = await fetch("Scripts/submit-action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "register", name, email, password })
      });

      const text = await response.text();
      console.log("Registration response:", text);

      const result = JSON.parse(text);

      if (result.success) {
        alert("Registration successful!");
        window.location.href = "dashboard.html";
      } else {
        alert(result.message || "Registration failed");
      }

    } catch (err) {
      console.error("Registration error:", err);
      alert("Unexpected error during registration.");
    }
  });
});
