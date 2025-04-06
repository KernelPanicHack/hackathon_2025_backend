@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <!-- Кнопка "Назад к списку" -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('index') }}" class="btn btn-light btn-rounded d-flex align-items-center shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>

        <!-- Основная карточка -->
        <div class="d-flex justify-content-center">
            <div class="card w-100 shadow-lg rounded-3" style="max-width: 900px;">
                <div class="card-header bg-primary text-white rounded-top-3">
                    <h3 class="mb-0 fw-semibold">
                        <i class="fas fa-tag me-2"></i>
                        {{ is_object($category) ? $category->name : $category }}
                    </h3>
                </div>

                <div class="card-body p-0">
                    <!-- Список операций с кастомным скроллом -->
                    <div class="scrollable-list" style="max-height: 500px;">
                        @forelse ($items as $operation)
                            <div class="product-item d-flex justify-content-between align-items-center px-4 py-3 hover-bg"
                                 id="operation-{{ $operation->id }}"
                                 data-item-id="{{ $operation->item->id }}">
                                <div class="d-flex align-items-center" style="width: 40%;">
                                    <i class="fas fa-cube text-muted me-3"></i>
                                    <span class="text-truncate">{{ $operation->item->name }}</span>
                                </div>

                                <button class="btn btn-outline-primary btn-pill px-4 d-flex align-items-center changeCategoryBtn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#categoryModal"
                                        data-operation-id="{{ $operation->id }}"
                                        data-current-category-id="{{ $operation->item->category_id }}">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    Изменить
                                </button>


                                <div class="text-end" style="width: 20%;">
            <span class="badge bg-success fw-normal fs-6">
                {{ number_format($operation->cost, 0, ',', ' ') }} ₽
            </span>
                                </div>
                            </div>
                            <hr class="m-0">
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted fs-5">Нет товаров в этой категории</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно выбора категории -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-primary">
                        <i class="fas fa-folder-open me-2"></i>Выберите категорию
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                @php
                    $colors = [
                        ['bg' => '#0d6efd', 'border' => '#0b5ed7'], // Синий
                        ['bg' => '#198754', 'border' => '#157347'], // Зелёный
                        ['bg' => '#ffc107', 'border' => '#e0a800'], // Жёлтый
                        ['bg' => '#dc3545', 'border' => '#bb2d3b'], // Красный
                    ];
                @endphp

                <div class="modal-body">
                    <div class="row g-3">
                        @foreach ($categories as $index => $cat)
                            <div class="col-6">
                                <button class="btn btn-category w-100 py-3"
                                        data-category-id="{{ $cat->id }}"
                                        style="background-color: {{ $colors[$index % count($colors)]['bg'] }};
                                               border-color: {{ $colors[$index % count($colors)]['border'] }};">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                                    {{ $cat->name }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer bg-light d-flex justify-content-center">
                    <button type="button" class="btn btn-primary" id="saveCategoryBtn">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .scrollable-list {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 #f8f9fa;
        }
        .scrollable-list::-webkit-scrollbar {
            width: 8px;
        }
        .scrollable-list::-webkit-scrollbar-track {
            background: #f8f9fa;
        }
        .scrollable-list::-webkit-scrollbar-thumb {
            background-color: #dee2e6;
            border-radius: 20px;
        }
        .hover-bg:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }
        .btn-category {
            border-radius: 15px;
            color: white !important;
            transition: transform 0.2s;
        }
        .btn-category:hover {
            transform: translateY(-2px);
        }
        .btn-rounded {
            border-radius: 50px;
        }
        .btn-pill {
            border-radius: 50px;
            transition: all 0.2s;
        }
        .btn-pill:hover {
            background-color: #0d6efd;
            color: white !important;
        }
        .product-item {
            transition: all 0.2s;
        }
        .btn-category.active {
            box-shadow: 0 0 0 2px #fff;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let selectedOperationId = null;
        let selectedCategoryId = null;

        // При клике на кнопку "Изменить" выбираем операцию
        document.querySelectorAll('.changeCategoryBtn').forEach(button => {
            button.addEventListener('click', function() {
                selectedOperationId = this.getAttribute('data-operation-id');
                selectedCategoryId = null;
                document.querySelectorAll('.btn-category').forEach(btn => btn.classList.remove('active'));
            });
        });

        // При выборе категории в модальном окне отмечаем кнопку как активную
        document.querySelectorAll('.btn-category').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.btn-category').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                selectedCategoryId = this.getAttribute('data-category-id');
            });
        });

        // При клике на "Сохранить" отправляем запрос на изменение категории
        document.getElementById('saveCategoryBtn').addEventListener('click', async function() {
            if (!selectedOperationId || !selectedCategoryId) {
                alert('Пожалуйста, выберите товар и новую категорию!');
                return;
            }

            try {
                const response = await fetch(`/operations/${selectedOperationId}/change-category`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ category_id: selectedCategoryId })
                });

                const data = await response.json();

                if (data.success) {
                    // Удаляем элемент операции из списка, делая его невидимым для пользователя
                    const operationElem = document.getElementById(`operation-${selectedOperationId}`);
                    if (operationElem) {
                        operationElem.remove();
                    }
                    // Закрываем модальное окно
                    const modalEl = document.getElementById('categoryModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                } else {
                    alert('Ошибка при смене категории.');
                }
            } catch (error) {
                alert('Ошибка: ' + error);
            }
        });

            function moveItemToCategory(itemId, newCategoryId) {
            fetch(`/items/${itemId}/update-category`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    category_id: newCategoryId
                })
            })
                .then(response => {
                    if (!response.ok) throw new Error('Ошибка при перемещении');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Элемент успешно перемещён!');
                        location.reload(); // или обновить только часть DOM, если нужно
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Не удалось переместить элемент');
                });
                let selectedOperationId = null;
                let selectedCategoryId = null;
                let currentCategoryId = null;

                document.querySelectorAll('.changeCategoryBtn').forEach(button => {
                    button.addEventListener('click', function () {
                        selectedOperationId = this.getAttribute('data-operation-id');
                        currentCategoryId = this.getAttribute('data-current-category-id');
                        selectedCategoryId = null;

                        // Очистка старых активных
                        document.querySelectorAll('.btn-category').forEach(btn => btn.classList.remove('active'));

                        // Подсвечиваем текущую категорию
                        const currentBtn = document.querySelector(`.btn-category[data-category-id="${currentCategoryId}"]`);
                        if (currentBtn) {
                            currentBtn.classList.add('active');
                            selectedCategoryId = currentCategoryId;
                        }
                    });
                });

            }

    </script>
@endpush
