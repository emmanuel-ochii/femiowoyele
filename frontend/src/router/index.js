import { createRouter, createWebHistory } from 'vue-router';
import PublicLayout from '../layouts/PublicLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: PublicLayout,
      // Every public page opens on a deep navy band, so the header sits
      // transparent over it until the visitor scrolls.
      meta: { transparentHeader: true },
      children: [
        { path: '', name: 'home', component: () => import('../domains/Home/HomePage.vue') },
        { path: 'about', name: 'about', component: () => import('../domains/About/AboutPage.vue') },
        { path: 'work', name: 'work', component: () => import('../domains/Work/WorkOverviewPage.vue') },
        { path: 'work/:pillarSlug', name: 'work-pillar', component: () => import('../domains/Work/PillarPage.vue') },
        { path: 'build-tomorrow', name: 'build-tomorrow', component: () => import('../domains/BuildTomorrow/BuildTomorrowPage.vue') },
        {
          path: 'build-tomorrow/:section',
          name: 'build-tomorrow-section',
          component: () => import('../domains/BuildTomorrow/BuildTomorrowSectionPage.vue'),
        },
        { path: 'research-ideas', name: 'research-ideas', component: () => import('../domains/ResearchIdeas/ResearchIdeasPage.vue') },
        {
          path: 'research-ideas/:slug',
          name: 'article-detail',
          component: () => import('../domains/ResearchIdeas/ArticleDetailPage.vue'),
        },
        { path: 'books', name: 'books', component: () => import('../domains/Books/BooksPage.vue') },
        {
          path: 'entrusted',
          name: 'entrusted-launch',
          component: () => import('../domains/Launch/EntrustedLaunchPage.vue'),
        },
        { path: 'books/:slug', name: 'book-detail', component: () => import('../domains/Books/BookDetailPage.vue') },
        { path: 'speaking', name: 'speaking', component: () => import('../domains/Speaking/SpeakingPage.vue') },
        { path: 'mentorship', name: 'mentorship', component: () => import('../domains/Mentorship/MentorshipPage.vue') },
        { path: 'impact', name: 'impact', component: () => import('../domains/Impact/ImpactPage.vue') },
        { path: 'media', name: 'media', component: () => import('../domains/Media/MediaPage.vue') },
        { path: 'journal', name: 'journal', component: () => import('../domains/Journal/JournalPage.vue') },
        { path: 'journal/:slug', name: 'journal-detail', component: () => import('../domains/Journal/JournalDetailPage.vue') },
        { path: 'contact', name: 'contact', component: () => import('../domains/Contact/ContactPage.vue') },
        { path: ':pathMatch(.*)*', name: 'not-found', component: () => import('../domains/NotFoundPage.vue') },
      ],
    },
    {
      path: '/admin/login',
      name: 'admin-login',
      component: () => import('../domains/Admin/AdminLoginPage.vue'),
    },
    {
      path: '/admin',
      component: AdminLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '', name: 'admin-dashboard', component: () => import('../domains/Admin/AdminDashboardPage.vue') },
        { path: 'content/:resource', name: 'admin-resource', component: () => import('../domains/Admin/AdminResourcePage.vue') },
      ],
    },
  ],
  scrollBehavior() {
    return { top: 0 };
  },
});

router.beforeEach((to) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'admin-login', query: { redirect: to.fullPath } };
  }

  if (to.name === 'admin-login' && auth.isAuthenticated) {
    return { name: 'admin-dashboard' };
  }

  return true;
});

export default router;
