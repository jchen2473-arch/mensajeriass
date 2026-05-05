let currentReceiver = '';
let refreshTimer = null;

const usersList = document.getElementById('users-list');
const chatTitle = document.getElementById('chat-title');
const chatBox = document.getElementById('messages');
const chatMessage = document.getElementById('chat-message');
const receiverInput = document.getElementById('receiver');

function setStatus(text, ok = false) {
    chatMessage.textContent = text;
    chatMessage.className = ok ? 'message ok' : 'message error';
}

async function parseJsonResponse(res) {
    const raw = await res.text();
    try {
        return JSON.parse(raw);
    } catch (_e) {
        throw new Error('Respuesta invalida del servidor');
    }
}

async function loadUsers() {
    const res = await fetch('api/users.php', { cache: 'no-store' });
    const data = await parseJsonResponse(res);

    if (!data.ok) {
        setStatus(data.message);
        return;
    }

    usersList.innerHTML = '';

    data.users.forEach((user) => {
        const li = document.createElement('li');
        li.textContent = user.username;
        li.addEventListener('click', () => startChat(user.username));
        usersList.appendChild(li);
    });
}

async function loadMessages() {
    if (!currentReceiver) return;

    const res = await fetch(`api/get_messages.php?receiver=${encodeURIComponent(currentReceiver)}`, { cache: 'no-store' });
    const data = await parseJsonResponse(res);

    if (!data.ok) {
        setStatus(data.message);
        return;
    }

    chatBox.innerHTML = '';

    data.messages.forEach((msg) => {
        const item = document.createElement('div');
        const mine = msg.emisor_username === data.my_username;

        item.className = `bubble ${mine ? 'mine' : 'theirs'}`;
        item.innerHTML = `
            <p>${msg.mensaje}</p>
            <small>${msg.emisor_username} - ${msg.enviado_en}</small>
        `;

        chatBox.appendChild(item);
    });

    chatBox.scrollTop = chatBox.scrollHeight;
}

function startChat(username) {
    currentReceiver = username;
    receiverInput.value = username;
    chatTitle.textContent = `Chat con @${username}`;
    setStatus('', true);
    loadMessages();

    if (refreshTimer) clearInterval(refreshTimer);
    refreshTimer = setInterval(loadMessages, 3000);
}

document.getElementById('select-user').addEventListener('click', () => {
    const username = receiverInput.value.trim();
    if (!username) {
        setStatus('Escribe un usuario para iniciar chat');
        return;
    }
    startChat(username);
});

document.getElementById('send-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!currentReceiver) {
        setStatus('Selecciona primero un usuario destino');
        return;
    }

    const text = document.getElementById('text').value.trim();
    if (!text) return;

    const formData = new FormData();
    formData.append('receiver', currentReceiver);
    formData.append('message', text);

    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    try {
        const res = await fetch('api/send_message.php', {
            method: 'POST',
            body: formData,
            cache: 'no-store'
        });

        const data = await parseJsonResponse(res);
        setStatus(data.message, data.ok);

        if (data.ok) {
            document.getElementById('text').value = '';
            loadMessages();
        }
    } catch (error) {
        setStatus(error.message || 'Error al enviar');
    } finally {
        submitBtn.disabled = false;
    }
});

loadUsers();
