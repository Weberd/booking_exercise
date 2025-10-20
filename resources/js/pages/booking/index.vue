<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">
                Бронирование услуг
            </h1>

            <!-- Выбор услуги -->
            <div v-if="step === 'service'" class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800">Выберите услугу</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div
                        v-for="service in services"
                        :key="service.id"
                        @click="selectService(service)"
                        class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all cursor-pointer"
                    >
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ service.name }}</h3>
                        <p class="text-gray-600">Длительность: {{ service.duration }} минут</p>
                    </div>
                </div>
            </div>

            <!-- Календарь и слоты -->
            <div v-if="step === 'date'" class="bg-white rounded-2xl shadow-xl p-8">
                <button
                    @click="step = 'service'; selectedService = null"
                    class="mb-6 text-indigo-600 hover:text-indigo-800 flex items-center"
                >
                    ← Назад к услугам
                </button>

                <h2 class="text-2xl font-semibold mb-2 text-gray-800">{{ selectedService.name }}</h2>
                <p class="text-gray-600 mb-6">Длительность: {{ selectedService.duration }} минут</p>

                <!-- Недельный календарь -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Выберите день</h3>
                    <div class="grid grid-cols-7 gap-2">
                        <div
                            v-for="day in weekDays"
                            :key="day.date"
                            @click="selectDate(day)"
                            :class="[
                'p-4 rounded-lg text-center cursor-pointer transition-all',
                day.isSunday ? 'bg-gray-100 text-gray-400 cursor-not-allowed' :
                selectedDate === day.date ? 'bg-indigo-600 text-white shadow-lg' :
                'bg-gray-50 hover:bg-indigo-100 text-gray-800'
              ]"
                        >
                            <div class="text-xs mb-1">{{ day.dayName }}</div>
                            <div class="text-lg font-semibold">{{ day.dayNumber }}</div>
                            <div class="text-xs">{{ day.month }}</div>
                        </div>
                    </div>
                </div>

                <!-- Доступные слоты -->
                <div v-if="selectedDate">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Доступные слоты</h3>
                    <div v-if="loading" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-600 border-t-transparent"></div>
                    </div>
                    <div v-else-if="slots.length === 0" class="text-center py-8 text-gray-500">
                        На этот день нет доступных слотов
                    </div>
                    <div v-else class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                        <button
                            v-for="slot in slots"
                            :key="slot.time"
                            @click="slot.available && selectSlot(slot)"
                            :disabled="!slot.available"
                            :class="[
                'py-3 px-4 rounded-lg font-semibold transition-all',
                slot.available
                  ? 'bg-green-100 text-green-800 hover:bg-green-200 hover:shadow-md'
                  : 'bg-gray-100 text-gray-400 cursor-not-allowed'
              ]"
                        >
                            {{ slot.time }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Форма бронирования -->
            <div v-if="step === 'form'" class="bg-white rounded-2xl shadow-xl p-8">
                <button
                    @click="step = 'date'; selectedSlot = null"
                    class="mb-6 text-indigo-600 hover:text-indigo-800 flex items-center"
                >
                    ← Назад к выбору времени
                </button>

                <h2 class="text-2xl font-semibold mb-6 text-gray-800">Оформление бронирования</h2>

                <div class="bg-indigo-50 rounded-lg p-4 mb-6">
                    <p class="text-gray-700"><strong>Услуга:</strong> {{ selectedService.name }}</p>
                    <p class="text-gray-700"><strong>Дата:</strong> {{ formatDate(selectedDate) }}</p>
                    <p class="text-gray-700"><strong>Время:</strong> {{ selectedSlot.time }}</p>
                </div>

                <form @submit.prevent="submitBooking" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Ваше имя</label>
                        <input
                            v-model="form.customer_name"
                            type="text"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                            placeholder="Иван Иванов"
                        />
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Номер телефона</label>
                        <input
                            v-model="form.customer_phone"
                            type="tel"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                            placeholder="+7 999 999-99-99"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full bg-indigo-600 text-white py-4 rounded-lg font-semibold hover:bg-indigo-700 transition-all disabled:bg-gray-400"
                    >
                        {{ submitting ? 'Бронирование...' : 'Забронировать' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Модальное окно успеха -->
        <div
            v-if="showSuccessModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click="closeModal"
        >
            <div
                class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl transform transition-all"
                @click.stop
            >
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Успешно забронировано!</h3>
                    <p class="text-gray-600 mb-6">Ваше бронирование подтверждено</p>
                    <button
                        @click="closeModal"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all"
                    >
                        Отлично
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const step = ref('service');
const services = ref([]);
const selectedService = ref(null);
const selectedDate = ref(null);
const selectedSlot = ref(null);
const slots = ref([]);
const loading = ref(false);
const submitting = ref(false);
const showSuccessModal = ref(false);

const form = ref({
    customer_name: '',
    customer_phone: '',
});

// Генерация дней недели (текущая неделя)
const weekDays = computed(() => {
    const days = [];
    const today = new Date();
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - today.getDay() + 1); // Понедельник

    for (let i = 0; i < 7; i++) {
        const date = new Date(startOfWeek);
        date.setDate(startOfWeek.getDate() + i);

        const dayNames = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        const monthNames = ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

        days.push({
            date: date.toISOString().split('T')[0],
            dayName: dayNames[date.getDay()],
            dayNumber: date.getDate(),
            month: monthNames[date.getMonth()],
            isSunday: date.getDay() === 0,
        });
    }

    return days;
});

onMounted(async () => {
    await loadServices();
});

const loadServices = async () => {
    try {
        const response = await axios.get('/api/v1/services');
        services.value = response.data.data ?? response.data;
    } catch (error) {
        console.error('Ошибка загрузки услуг:', error);
    }
};

const selectService = (service) => {
    selectedService.value = service;
    step.value = 'date';
};

const selectDate = async (day) => {
    if (day.isSunday) return;

    selectedDate.value = day.date;
    loading.value = true;

    try {
        const response = await axios.get(`/api/v1/services/${selectedService.value.id}/slots/${day.date}`);
        slots.value = response.data;
    } catch (error) {
        console.error('Ошибка загрузки слотов:', error);
    } finally {
        loading.value = false;
    }
};

const selectSlot = (slot) => {
    selectedSlot.value = slot;
    step.value = 'form';
};

const submitBooking = async () => {
    submitting.value = true;

    try {
        const response = await axios.post('/api/v1/bookings', {
            service_id: selectedService.value.id,
            customer_name: form.value.customer_name,
            customer_phone: form.value.customer_phone,
            booking_date: selectedDate.value,
            start_time: selectedSlot.value.time,
        });

        if (response.data.success) {
            showSuccessModal.value = true;
        }
    } catch (error) {
        if (error.response && error.response.status === 409) {
            alert('К сожалению, это время уже занято. Пожалуйста, выберите другой слот.');
            step.value = 'date';
            await selectDate({ date: selectedDate.value, isSunday: false });
        } else {
            alert('Произошла ошибка при бронировании. Попробуйте снова.');
        }
        console.error('Ошибка бронирования:', error);
    } finally {
        submitting.value = false;
    }
};

const closeModal = () => {
    showSuccessModal.value = false;
    // Сброс к начальному экрану
    step.value = 'service';
    selectedService.value = null;
    selectedDate.value = null;
    selectedSlot.value = null;
    form.value = {
        customer_name: '',
        customer_phone: '',
    };
};

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
};
</script>
