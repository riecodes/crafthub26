const body = document.querySelector("body"),
    sidebar = document.querySelector(".sidebar"),
    toggle = document.querySelector(".toggle"),
    modeSwitch = document.querySelector(".toggle-switch"),
    modeText = document.querySelector(".mode-text");

// Function to set the mode
function setMode(mode) {
    if (mode === 'dark') {
        body.classList.add("dark");
        sidebar.classList.add("dark");
        modeText.innerText = "Light Mode";
        document.cookie = "darkMode=true; path=/; max-age=31536000";
    } else {
        body.classList.remove("dark");
        sidebar.classList.remove("dark");
        modeText.innerText = "Dark Mode";
        document.cookie = "darkMode=false; path=/; max-age=31536000";
    }
}

// Immediately apply dark mode if set in cookie to prevent flash of light mode
const darkMode = document.cookie.split('; ').find(row => row.startsWith('darkMode='));
if (darkMode && darkMode.split('=')[1] === 'true') {
    setMode('dark');
} else {
    setMode('light');
}

// Prevent any flash of light mode by applying dark mode before the page is rendered
document.documentElement.classList.add('dark'); // Add dark class to html element
if (darkMode && darkMode.split('=')[1] === 'true') {
    body.classList.add("dark");
    sidebar.classList.add("dark");
}

// Ensure dark mode is applied before the page is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    const currentPage = window.location.pathname.split("/").pop();
    const menuLinks = document.querySelectorAll('.sidebar .menu-links .nav-link');

    menuLinks.forEach(link => {
        const anchor = link.querySelector('a');
        if (anchor.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
});

toggle.addEventListener("click", () => {
    sidebar.classList.toggle("close");
    document.querySelector("#content").classList.toggle("expand");
});

modeSwitch.addEventListener("click", () => {
    body.classList.toggle("dark");
    setMode(body.classList.contains("dark") ? 'dark' : 'light');
});

