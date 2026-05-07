const tabs = document.querySelectorAll('.tab');
const forms = document.querySelectorAll('.form');
const message = document.getElementById('auth-message');

function setMessage(text, ok = false) {
    message.textContent = text;
    message.className = ok ? 'message ok' : 'message error';
}

async function postForm(url, formData) {
    const res = await fetch(url, {
        method: 'POST',
        body: formData,
        cache: 'no-store',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const raw = await res.text();
    try {
        return JSON.parse(raw);
    } catch (_error) {
        throw new Error('Respuesta invalida del servidor. Revisa que Apache y MySQL esten iniciados.');
    }
}

tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
        tabs.forEach((t) => t.classList.remove('active'));
        forms.forEach((f) => f.classList.remove('active'));

        tab.classList.add('active');
        document.getElementById(tab.dataset.target).classList.add('active');
        message.textContent = '';
    });
});

document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    try {
        const data = await postForm('api/login.php', formData);
        setMessage(data.message, data.ok);

        if (data.ok) {
            window.location.href = 'chat.php';
        }
    } catch (error) {
        setMessage(error.message || 'Error al iniciar sesion');
    } finally {
        submitBtn.disabled = false;
    }
});

document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    try {
        const data = await postForm('api/register.php', formData);
        setMessage(data.message, data.ok);

        if (data.ok) {
            e.target.reset();
        }
    } catch (error) {
        setMessage(error.message || 'Error al crear la cuenta');
    } finally {
        submitBtn.disabled = false;
    }
});

document.getElementById('reset-form').addEventListener('submit', (e) => {
    e.preventDefault();
    setMessage('La recuperación de contraseña aún no está disponible. Usa un usuario existente o crea una cuenta.', false);
});
