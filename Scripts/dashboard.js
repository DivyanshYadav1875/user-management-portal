function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
  return null;
}

const rememberedUser = getCookie('remembered_user');

if (!rememberedUser) {
  console.log("No remembered_user cookie found.");
  window.location.href = 'index.html';
} else {
  console.log("Found remembered_user:", rememberedUser);
  
  fetch('Scripts/user-check.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name: rememberedUser })
  })
  .then(res => {
    if (!res.ok) throw new Error("Network response was not OK");
    return res.json();
  })
  .then(data => {
    console.log("User-check response:", data);
    if (data.success) {
      document.getElementById("status").innerText = 
        `Welcome ${data.user.username} (User Type: ${data.user.user_type})`;
    } else {
      console.warn("Verification failed:", data.message);
      window.location.href = 'index.html';
    }
  })
  .catch(err => {
    console.error("Error verifying login:", err);
    alert("Error checking login. Try again.");
    window.location.href = 'index.html';
  });
}
