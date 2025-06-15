<script setup>
import {onMounted} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import {useToast} from 'vue-toastification';
import {useAuthStore} from '@/stores/auth';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const authStore = useAuthStore();

onMounted(() => {
  const {token, name, email, is_activated, cfo, area, group, campaign_id, id, campaign} = route.query;

  if (!token) {
    toast.error('Redirecting to account activation.');
    return router.push('/activate');
  }

  authStore.setUser(
      {
        id,
        name,
        email,
        is_activated,
        cfo: cfo || '',
        area: area || '',
        group: group || ''
      },
      token
  );

  localStorage.setItem('campaign_id', campaign_id || '');
  localStorage.setItem('campaign', campaign || '');

  toast.success(`Welcome, ${name}!`);
  router.push('/maps');
});
</script>