@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <!-- Кнопка закрытия (иконка "Х") -->
        <div class="d-flex justify-content-end">
            <button class="btn btn-outline-secondary" onclick="window.history.back();">
                Закрыть (X)
            </button>
        </div>

        <!-- Центрированный блок с карточкой, занимающей большую часть экрана -->
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100" style="max-width: 900px;">
                <div class="card-header">
                    <!-- Название категории -->
                    <h4 class="mb-0">
                        {{ is_object($category) ? $category->name : $category }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Прокручиваемый блок со списком товаров -->
                    <div style="max-height: 400px; overflow-y: auto;">
                        @forelse ($products as $product)
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <span>{{ $product->name }}</span>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                    Изменить категорию
                                </button>
                                <span>{{ $product->price }} ₽</span>
                            </div>
                        @empty
                            <p class="text-center">Товары не найдены</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для выбора категории -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Заголовок модального окна -->
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Выберите нужную категорию</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Содержимое модального окна -->
                <div class="modal-body">
                    <!-- Кнопки категорий -->
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-danger">Одежда</button>
                        <button class="btn btn-outline-success">Еда</button>
                        <button class="btn btn-outline-primary">Такси</button>
                        <button class="btn btn-outline-warning">Аренда</button>
                    </div>
                </div>

                <!-- Нижняя часть модального окна -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    {{-- Здесь можно добавить кнопку "Сохранить" с нужной логикой --}}
                </div>
            </div>
        </div>
    </div>
@endsection
