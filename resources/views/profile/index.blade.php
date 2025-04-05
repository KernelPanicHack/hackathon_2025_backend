{{-- resources/views/expenses.blade.php --}}
@extends('layouts.app')

@push('styles')
    {{-- Подключение Font Awesome, Bootstrap и кастомных стилей --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .chart-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chart-wrapper button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .chart-container {
            max-width: 300px;
            margin: 0 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0" id="monthTitle">{{ \Carbon\Carbon::parse($currentMonth . '-01')->translatedFormat('F Y') }}</h2>
            <a href="#" class="btn btn-outline-secondary">Выход</a>
        </div>

        <div class="row">
            <!-- Диаграмма -->
            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                            <div class="chart-container">
                                <canvas id="expensesPieChart"></canvas>
                            </div>
                            <button id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="mt-3 text-center" style="font-size: 1.5rem; font-weight: bold;" id="totalExpenses">
                            {{-- Общая сумма расходов --}}
                        </div>
                    </div>
                </div>
            </div>
            <!-- Список расходов -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Расходы</h5>
                        <ul class="list-group list-group-flush" id="expensesList">
                            {{-- Динамический список --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- История и прогноз -->
        <div class="row">
            <!-- История -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">История</h5>
                        <div class="history-list" style="max-height: 300px; overflow-y: auto;">
                            <ul class="list-group list-group-flush" id="historyList">
                                {{-- Динамический список истории --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Прогноз -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Прогноз</h5>
                        <canvas id="forecastLineChart" style="max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Подключаем Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Инициализация глобальных переменных
        let currentMonth = "{{ $currentMonth }}";
        let pieChart, forecastChart;
        const presetColors = [
            'hsl(10, 80%, 65%)',
            'hsl(40, 80%, 65%)',
            'hsl(80, 80%, 65%)',
            'hsl(120, 80%, 65%)',
            'hsl(160, 80%, 65%)',
            'hsl(200, 80%, 65%)',
            'hsl(240, 80%, 65%)',
            'hsl(280, 80%, 65%)',
            'hsl(320, 80%, 65%)'
        ];

        // Функция перемешивания массива
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        // Функция для инициализации/обновления диаграмм
        function initCharts(labels, dataValues, forecastData) {
            const uniqueColors = shuffleArray([...presetColors]).slice(0, labels.length);

            if (pieChart) {
                pieChart.data.labels = labels;
                pieChart.data.datasets[0].data = dataValues;
                pieChart.data.datasets[0].backgroundColor = uniqueColors;
                pieChart.update();
            } else {
                const ctxExpenses = document.getElementById('expensesPieChart').getContext('2d');
                pieChart = new Chart(ctxExpenses, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: uniqueColors
                        }]
                    },
                    options: {
                        cutout: '60%'
                    }
                });
            }

            if (forecastChart) {
                forecastChart.data.datasets[0].data = forecastData;
                forecastChart.update();
            } else {
                const ctxForecast = document.getElementById('forecastLineChart').getContext('2d');
                forecastChart = new Chart(ctxForecast, {
                    type: 'line',
                    data: {
                        labels: ['Неделя 1', 'Неделя 2', 'Неделя 3', 'Неделя 4'],
                        datasets: [{
                            label: 'Прогноз',
                            data: forecastData,
                            borderColor: '#58A761',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } }
                    }
                });
            }

            document.querySelectorAll('#expensesList li').forEach((item, index) => {
                const icon = item.querySelector('i');
                if (icon && uniqueColors[index]) {
                    icon.style.color = uniqueColors[index];
                }
            });
        }

        // Функция обновления страницы новыми данными
        function updatePage(data) {
            const chartLabels = data.chartLabels && data.chartLabels.length ? data.chartLabels : ['Нет данных'];
            const chartData = data.chartData && data.chartData.length ? data.chartData : [0];
            const forecast = data.forecast && data.forecast.length ? data.forecast : [0, 0, 0, 0];
            const history = data.history && data.history.length ? data.history : [];

            const monthDate = new Date(data.month + '-01');
            const options = { month: 'long', year: 'numeric' };
            document.getElementById('monthTitle').innerText = monthDate.toLocaleDateString('ru-RU', options);

            const total = chartData.reduce((sum, val) => sum + parseFloat(val), 0);
            document.getElementById('totalExpenses').innerText = total;

            const expensesList = document.getElementById('expensesList');
            expensesList.innerHTML = '';
            chartLabels.forEach((label, index) => {
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                li.innerHTML = `<span><i class="fas fa-circle me-2"></i>${label}</span><span>${chartData[index]}</span>`;
                expensesList.appendChild(li);
            });

            const historyList = document.getElementById('historyList');
            historyList.innerHTML = '';
            history.forEach(item => {
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'justify-content-between');
                li.innerHTML = `<span>${item.date} - ${item.category}</span><span>${item.amount}</span>`;
                historyList.appendChild(li);
            });

            initCharts(chartLabels, chartData, forecast);
        }

        // Функция уведомления (показывает сообщение в углу экрана)
        function showNotification(message) {
            let notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerText = message;
            notification.style.position = 'fixed';
            notification.style.bottom = '20px';
            notification.style.right = '20px';
            notification.style.background = '#f0ad4e';
            notification.style.color = '#fff';
            notification.style.padding = '10px 20px';
            notification.style.borderRadius = '5px';
            notification.style.boxShadow = '0 0 10px rgba(0,0,0,0.3)';
            notification.style.zIndex = '9999';
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        // Функция для AJAX-запроса данных по выбранному месяцу
        async function fetchMonthData(month) {
            try {
                const response = await fetch("{{ route('expenses.monthData') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ month: month })
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.error) {
                        showNotification(data.message);
                        return false;
                    } else {
                        updatePage(data);
                        return true;
                    }
                } else {
                    showNotification('Нету данных за предыдущий месяц');
                    return false;
                }
            } catch (error) {
                showNotification('Ошибка: ' + error);
                return false;
            }
        }

        // Обработчик нажатия стрелки "назад" с проверкой на наличие данных
        document.getElementById('prevMonth').addEventListener('click', async function() {
            let date = new Date(currentMonth + '-01');
            date.setMonth(date.getMonth() - 1);
            const newMonth = date.toISOString().slice(0, 7);
            const oldMonth = currentMonth;
            currentMonth = newMonth;
            const success = await fetchMonthData(currentMonth);
            if (!success) {
                // Если данных нет – возвращаем предыдущий месяц
                currentMonth = oldMonth;
            }
        });

        // Обработчик стрелки "вперёд"
        document.getElementById('nextMonth').addEventListener('click', function() {
            let date = new Date(currentMonth + '-01');
            date.setMonth(date.getMonth() + 1);
            currentMonth = date.toISOString().slice(0, 7);
            fetchMonthData(currentMonth);
        });

        // Инициализация данных при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            const initialData = @json($data);
            updatePage(initialData);
        });
    </script>
@endpush

