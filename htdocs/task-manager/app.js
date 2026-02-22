// Task Class
class Task {
    constructor(text) {
        this.id = Date.now().toString();
        this.text = text;
        this.completed = false;
        this.createdAt = new Date();
    }
}

// App Controller
const App = {
    tasks: [],
    filter: 'all',
    sortBy: 'newest',

    init() {
        this.loadTasks();
        this.cacheDOM();
        this.bindEvents();
        this.render();
    },

    cacheDOM() {
        this.form = document.getElementById('task-form');
        this.input = document.getElementById('task-input');
        this.list = document.getElementById('task-list');
        this.totalCount = document.getElementById('total-tasks');
        this.pendingCount = document.getElementById('pending-tasks');
        this.emptyState = document.getElementById('empty-state');
        this.filterBtns = document.querySelectorAll('.filter-btn');
        this.sortSelect = document.getElementById('sort-tasks');
    },

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.addTask(e));

        this.filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.filter = btn.dataset.filter;
                this.render();
            });
        });

        this.sortSelect.addEventListener('change', (e) => {
            this.sortBy = e.target.value;
            this.render();
        });
    },

    loadTasks() {
        const saved = localStorage.getItem('nextask_tasks');
        if (saved) {
            this.tasks = JSON.parse(saved).map(t => {
                t.createdAt = new Date(t.createdAt);
                return t;
            });
        }
    },

    saveTasks() {
        localStorage.setItem('nextask_tasks', JSON.stringify(this.tasks));
    },

    addTask(e) {
        e.preventDefault();
        const text = this.input.value.trim();
        if (!text) return;

        const newTask = new Task(text);
        this.tasks.push(newTask);
        this.input.value = '';
        this.saveTasks();
        this.render();
    },

    toggleTask(id) {
        this.tasks = this.tasks.map(t => {
            if (t.id === id) t.completed = !t.completed;
            return t;
        });
        this.saveTasks();
        this.render();
    },

    deleteTask(id) {
        this.tasks = this.tasks.filter(t => t.id !== id);
        this.saveTasks();
        this.render();
    },

    getFilteredTasks() {
        let filtered = [...this.tasks];

        if (this.filter === 'pending') {
            filtered = filtered.filter(t => !t.completed);
        } else if (this.filter === 'completed') {
            filtered = filtered.filter(t => t.completed);
        }

        return filtered.sort((a, b) => {
            if (this.sortBy === 'newest') return b.createdAt - a.createdAt;
            if (this.sortBy === 'oldest') return a.createdAt - b.createdAt;
            if (this.sortBy === 'alphabetical') return a.text.localeCompare(b.text);
            return 0;
        });
    },

    updateStats() {
        this.totalCount.textContent = this.tasks.length;
        this.pendingCount.textContent = this.tasks.filter(t => !t.completed).length;
    },

    render() {
        const filteredTasks = this.getFilteredTasks();
        this.list.innerHTML = '';

        if (filteredTasks.length === 0) {
            this.emptyState.style.display = 'block';
        } else {
            this.emptyState.style.display = 'none';
            filteredTasks.forEach(task => {
                const li = document.createElement('li');
                li.className = `task-item ${task.completed ? 'completed' : ''}`;
                li.innerHTML = `
                    <div class="checkbox-container" onclick="App.toggleTask('${task.id}')">
                        <div class="task-checkbox"></div>
                    </div>
                    <span class="task-text">${task.text}</span>
                    <button class="btn-delete" onclick="App.deleteTask('${task.id}')">
                        &times;
                    </button>
                `;
                this.list.appendChild(li);
            });
        }

        this.updateStats();
    }
};

// Initialize the app
App.init();
