const todoForm = document.getElementById('todo-form');
const todoInput = document.getElementById('todo-input');
const todoList = document.getElementById('todo-list');

function createTodoElement(text) {
  const li = document.createElement('li');
  li.className = 'todo-item';

  const span = document.createElement('span');
  span.className = 'todo-text';
  span.textContent = text;
  span.addEventListener('click', () => {
    li.classList.toggle('completed');
  });

  const actions = document.createElement('div');
  actions.className = 'todo-actions';

  const deleteBtn = document.createElement('button');
  deleteBtn.innerHTML = '🗑️';
  deleteBtn.title = 'Sil';
  deleteBtn.addEventListener('click', () => {
    li.remove();
  });

  actions.appendChild(deleteBtn);
  li.appendChild(span);
  li.appendChild(actions);
  return li;
}

todoForm.addEventListener('submit', function(e) {
  e.preventDefault();
  const value = todoInput.value.trim();
  if (value) {
    const todoEl = createTodoElement(value);
    todoList.appendChild(todoEl);
    todoInput.value = '';
    todoInput.focus();
  }
}); 