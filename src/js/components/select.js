class CustomSelect {
  constructor(selector, options = {}) {
    this.select = document.querySelector(selector);
    if (!this.select) return;

    this.options = {
      placeholder: 'Выберите...',
      onChange: null,
      ...options
    };

    this.init();
  }

  init() {
    this.select.style.display = 'none';
    this.wrapper = document.createElement('div');
    this.wrapper.className = 'custom-select-wrapper';

    this.selected = document.createElement('div');
    this.selected.className = 'custom-select-selected';
    this.selected.textContent = this.options.placeholder;

    this.dropdown = document.createElement('div');
    this.dropdown.className = 'custom-select-dropdown';

    Array.from(this.select.options).forEach((opt) => {
      const item = document.createElement('div');
      item.className = 'custom-select-option';
      item.textContent = opt.text;
      item.dataset.value = opt.value;

      // Check if window.location.search has orderby, use as selected by default
      // If not, check if the option is selected and not in the URL
      const params = new URLSearchParams(window.location.search);
      const getOrderBy = params.get('orderby');
      if (getOrderBy && getOrderBy === opt.value) {
        this.selected.textContent = opt.text;
        item.classList.add('active');
      } else if (opt.selected && !getOrderBy) {
        this.selected.textContent = opt.text;
        item.classList.add('active');
      }

      item.addEventListener('click', () => this.selectOption(item));
      this.dropdown.appendChild(item);
    });

    this.selected.addEventListener('click', () => this.toggle());
    this.wrapper.appendChild(this.selected);
    this.wrapper.appendChild(this.dropdown);
    this.select.parentNode.insertBefore(this.wrapper, this.select);

    document.addEventListener('click', (e) => {
      if (!this.wrapper.contains(e.target)) this.close();
    });
  }

  toggle() {
    this.dropdown.classList.toggle('show');
    this.selected.classList.toggle('active');
  }

  close() {
    this.dropdown.classList.remove('show');
    this.selected.classList.remove('active');
  }

  selectOption(item) {
    this.dropdown
      .querySelectorAll('.custom-select-option')
      .forEach((opt) => opt.classList.remove('active'));
    item.classList.add('active');
    this.selected.textContent = item.textContent;
    this.select.value = item.dataset.value;
    this.close();
    if (typeof this.options.onChange === 'function') {
      this.options.onChange(this.select.value);
    }
    this.select.dispatchEvent(new Event('change'));
  }
}

new CustomSelect('.my-select', {
  placeholder: 'Выберите опцию'
});