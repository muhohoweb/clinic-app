<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import {dashboard} from '@/routes';
import patients from '@/routes/patients';
import payments from '@/routes/payments';
import visits from '@/routes/visits';
import balances from '@/routes/balances';
import {type NavItem} from '@/types';
import {Link} from '@inertiajs/vue3';
import {BookOpen, Folder, Handshake, LayoutGrid, User2Icon, UsersIcon} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
  {
    title: 'Dashboard',
    href: dashboard(),
    icon: LayoutGrid,
  },
  {
    title: 'Patients',
    href: patients.index(),
    icon: User2Icon,
  },
  {
    title: 'Visits',
    href: visits.index(),
    icon: UsersIcon,
  },
  {
    title: 'Payments',
    href: payments.index(),
    icon: LayoutGrid,
  },
  {
    title: 'Balances',
    href: balances.index(),
    icon: Handshake,
  },
];

const footerNavItems: NavItem[] = [

];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="dashboard()">
              <AppLogo/>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems"/>
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems"/>
      <NavUser/>
    </SidebarFooter>
  </Sidebar>
  <slot/>
</template>
