import { router } from "@inertiajs/vue3";
import { ref, Ref } from "vue";

interface TaskForm {
  title: string;
  description: string;
  difficulty_level: number;
  reset_frequency: number;
  is_positive: boolean;
  tags: string;
  start_date: string;
  is_completed: boolean;
  is_deadline_task: boolean;
  experience_reward: number;
  type: 'habit' | 'daily' | 'todo';
}

interface Props {
  difficulties: Array<{ difficulty_level: number }>;
  resetConfigs: Array<{ id: number }>;
}

export function createTask(props: Props) {
  const form: Ref<TaskForm> = ref({
    title: '',
    description: '',
    difficulty_level: props.difficulties?.[0]?.difficulty_level || 2,
    reset_frequency: props.resetConfigs?.[0]?.id || 1,
    is_positive: true,
    tags: '',
    start_date: new Date().toISOString().slice(0, 10),
    is_completed: false,
    is_deadline_task: false,
    experience_reward: 10,
    type: 'habit',
  });

  const loading = ref(false);

  const submit = (resetForm: () => void, emit: (event: 'created') => void) => {
    loading.value = true;
    router.post('/tasks', {
      ...form.value,
      tags: form.value.tags.split(',').map(t => t.trim()).filter(Boolean),
    }, {
      onSuccess: () => {
        loading.value = false;
        resetForm();
        emit('created');
      },
      onError: () => {
        loading.value = false;
      }
    });
  };

  return {
    form,
    loading,
    submit
  };
}