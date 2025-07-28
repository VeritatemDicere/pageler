document.addEventListener('DOMContentLoaded', function() {
    // Takvim: Gün seçici ekle
    const calendarDiv = document.getElementById('calendar-view');
    if (calendarDiv) {
        const today = new Date().toISOString().slice(0, 10);
        calendarDiv.innerHTML = `<input type='date' id='calendar-date' value='${today}' />`;
        document.getElementById('calendar-date').addEventListener('change', function() {
            reloadCurrentList();
        });
    }

    // Listeleri yükle
    fetch('api/lists.php')
        .then(res => res.json())
        .then(data => {
            const ul = document.getElementById('lists');
            ul.innerHTML = '';
            data.forEach(list => {
                const li = document.createElement('li');
                li.textContent = `${list.emoji || ''} ${list.name}`;
                li.style.borderLeft = `8px solid ${list.color || '#007aff'}`;
                li.onclick = () => selectList(list.id);
                ul.appendChild(li);
            });
        });

    let currentListId = null;
    let notificationEnabled = false;

    window.selectList = function(listId) {
        currentListId = listId;
        // Bildirim durumu kontrolü
        fetch(`api/notifications.php?list_id=${listId}`)
            .then(res => res.json())
            .then(data => {
                notificationEnabled = !!data.enabled;
                showNotificationToggle();
            });
        // Sectionları yükle
        fetch(`api/sections.php?list_id=${listId}`)
            .then(res => res.json())
            .then(sections => {
                const secDiv = document.getElementById('sections');
                secDiv.innerHTML = '';
                sections.forEach(section => {
                    const sec = document.createElement('div');
                    sec.className = 'section';
                    sec.innerHTML = `<h3>${section.name}</h3><button onclick=\"addTaskPrompt(${listId}, ${section.id})\">+ Görev</button><div id=\"tasks-section-${section.id}\"></div>`;
                    secDiv.appendChild(sec);
                    loadTasks(null, section.id);
                });
                // Ana listeye bağlı, section'sız görevler
                loadTasks(listId, null);
            });
    }

    function showNotificationToggle() {
        let sidebar = document.querySelector('.sidebar');
        let btn = document.getElementById('notify-toggle-btn');
        if (!btn) {
            btn = document.createElement('button');
            btn.id = 'notify-toggle-btn';
            sidebar.insertBefore(btn, sidebar.children[1]);
        }
        btn.textContent = notificationEnabled ? '🔔 Bildirim Açık' : '🔕 Bildirim Kapalı';
        btn.style.background = notificationEnabled ? '#4cd964' : '#e0e0e0';
        btn.onclick = function() {
            fetch('api/notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ list_id: currentListId, enabled: !notificationEnabled })
            }).then(() => {
                notificationEnabled = !notificationEnabled;
                showNotificationToggle();
            });
        }
    }

    window.loadTasks = function(listId, sectionId) {
        let url = 'api/tasks.php?';
        if (sectionId) url += `section_id=${sectionId}`;
        else if (listId) url += `list_id=${listId}`;
        // Takvim filtresi
        const dateInput = document.getElementById('calendar-date');
        if (dateInput && dateInput.value) {
            url += `&due_date=${dateInput.value}`;
        }
        fetch(url)
            .then(res => res.json())
            .then(tasks => {
                let container;
                if (sectionId) {
                    container = document.getElementById(`tasks-section-${sectionId}`);
                } else {
                    container = document.getElementById('tasks');
                    container.innerHTML = '<h3>Diğer Görevler</h3>';
                }
                container.innerHTML = tasks.map(task => renderTaskCard(task)).join('');
            });
    }

    function renderTaskCard(task) {
        let statusBtn = '';
        if (task.status === 'pending') {
            statusBtn = `<button onclick=\"updateTaskStatus(${task.id}, 'completed')\">Tamamla</button>`;
        } else if (task.status === 'completed') {
            statusBtn = `<button onclick=\"updateTaskStatus(${task.id}, 'pending')\">Geri Al</button>`;
        }
        // Hızlı taşıma butonları
        let moveBtns = `
            <button onclick=\"moveTask(${task.id}, 'urgent')\">Acil</button>
            <button onclick=\"moveTask(${task.id}, 'issue')\">Sorun</button>
        `;
        // Silme butonu
        let deleteBtn = `<button onclick=\"deleteTask(${task.id})\">Sil</button>`;
        return `<div class='task-card ${task.status}'>
            <b>${task.title}</b> <span>${task.due_date || ''}</span><br>
            <small>${task.label || ''}</small><br>
            ${statusBtn} ${moveBtns} ${deleteBtn}
        </div>`;
    }

    window.updateTaskStatus = function(id, status) {
        fetch('api/tasks.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status })
        }).then(() => reloadCurrentList());
    }

    window.moveTask = function(id, status) {
        fetch('api/tasks.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status })
        }).then(() => reloadCurrentList());
    }

    window.deleteTask = function(id) {
        if (!confirm('Görev silinsin mi?')) return;
        fetch('api/tasks.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        }).then(() => reloadCurrentList());
    }

    function reloadCurrentList() {
        if (currentListId) selectList(currentListId);
    }

    // Yeni liste ekleme butonu
    document.getElementById('add-list-btn').onclick = function() {
        const name = prompt('Liste adı:');
        if (!name) return;
        fetch('api/lists.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name })
        }).then(() => location.reload());
    }

    // Yeni section ekleme fonksiyonu
    window.addSectionPrompt = function() {
        const name = prompt('Bölüm adı:');
        if (!name || !currentListId) return;
        fetch('api/sections.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ list_id: currentListId, name })
        }).then(() => selectList(currentListId));
    }

    // Yeni görev ekleme fonksiyonu
    window.addTaskPrompt = function(listId, sectionId) {
        const title = prompt('Görev başlığı:');
        if (!title) return;
        let due_date = prompt('Tarih (YYYY-MM-DD):', document.getElementById('calendar-date')?.value || '');
        if (due_date && !/^\d{4}-\d{2}-\d{2}$/.test(due_date)) due_date = '';
        fetch('api/tasks.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ list_id: listId, section_id: sectionId, title, due_date })
        }).then(() => {
            if (notificationEnabled) {
                const audio = document.getElementById('notify-sound');
                if (audio) audio.play();
            }
            if (sectionId) loadTasks(null, sectionId);
            else loadTasks(listId, null);
        });
    }

    // İstatistikler (Chart.js)
    const statsDiv = document.querySelector('.stats');
    if (statsDiv) {
        statsDiv.innerHTML = `
            <div style='display:flex;gap:8px;justify-content:center;'>
                <button id='stats-daily'>Günlük</button>
                <button id='stats-weekly'>Haftalık</button>
                <button id='stats-monthly'>Aylık</button>
            </div>
            <canvas id="statsChart"></canvas>
        `;
        let statsChart;
        function loadStats(type = 'daily') {
            fetch('api/stats.php?type=' + type)
                .then(res => res.json())
                .then(data => {
                    let labels = [];
                    let values = [];
                    if (type === 'daily') {
                        labels = data.map(d => d.date);
                        values = data.map(d => d.count);
                    } else if (type === 'weekly') {
                        labels = data.map(d => 'Hafta ' + d.week);
                        values = data.map(d => d.count);
                    } else if (type === 'monthly') {
                        labels = data.map(d => d.month);
                        values = data.map(d => d.count);
                    }
                    if (statsChart) statsChart.destroy();
                    statsChart = new Chart(document.getElementById('statsChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Tamamlanan Görev',
                                data: values,
                                backgroundColor: '#007aff',
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                });
        }
        document.getElementById('stats-daily').onclick = () => loadStats('daily');
        document.getElementById('stats-weekly').onclick = () => loadStats('weekly');
        document.getElementById('stats-monthly').onclick = () => loadStats('monthly');
        loadStats('daily');
    }
}); 