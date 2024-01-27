<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

defineProps<{
    balance?: Array;
}>();

const form = useForm({
    amount: 0,
});

const submit = () => {
    form.post(route("add_funds"), {
        onFinish: () => {
            form.reset("amount");
        },
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4"
                >
                    <div class="flex justify-end">
                        <div class="p-6 text-gray-900 text-2xl">
                            Balance: {{ balance["available"]["amount"] }}
                            {{ balance["available"]["currency"] }}
                        </div>
                    </div>

                    <div>
                        <form @submit.prevent="submit">
                            <input
                                type="number"
                                name="amount"
                                v-model="form.amount"
                                min="1"
                                step="1"
                                class="rounded"
                            />
                            <button
                                class="bg-slate-700 text-white p-2 rounded ml-2"
                            >
                                Add Balance
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
