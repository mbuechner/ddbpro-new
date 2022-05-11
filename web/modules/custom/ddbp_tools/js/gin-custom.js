// Selects end date value based on start date.
const startDate = document.querySelector('.form-item--field-date-0-value-date input');
const endDate = document.querySelector('.form-item--field-date-0-end-value-date input');

startDate.addEventListener('change', () => endDate.value = startDate.value);
