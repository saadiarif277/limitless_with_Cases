/**
 * App Calendar Events
 */

'use strict';

let date = new Date();
let nextDay = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
// prettier-ignore
let nextMonth = date.getMonth() === 11 ? new Date(date.getFullYear() + 1, 0, 1) : new Date(date.getFullYear(), date.getMonth() + 1, 1);
// prettier-ignore
let prevMonth = date.getMonth() === 11 ? new Date(date.getFullYear() - 1, 0, 1) : new Date(date.getFullYear(), date.getMonth() - 1, 1);

let events = [
  {
    id: 1,
    title: 'Neuralscan',
    start: date,
    end: nextDay,
    extendedProps: {
      calendar: 'Chicago Pain Center, LTD'
    }
  },
  {
    id: 2,
    title: 'Neuralscan',
    start: new Date(date.getFullYear(), date.getMonth() + 1, -11),
    end: new Date(date.getFullYear(), date.getMonth() + 1, -10),
    extendedProps: {
      calendar: 'Chicago Pain Center, LTD'
    }
  },
  {
    id: 3,
    title: 'Follow-up',
    start: new Date(date.getFullYear(), date.getMonth() + 1, -9),
    end: new Date(date.getFullYear(), date.getMonth() + 1, -7),
    extendedProps: {
      calendar: 'Texas mentor Clinic'
    }
  },
  {
    id: 4,
    title: 'Neuralscan',
    start: new Date(date.getFullYear(), date.getMonth() + 1, -11),
    end: new Date(date.getFullYear(), date.getMonth() + 1, -10),
    extendedProps: {
      calendar: 'Texas mentor Clinic'
    }
  },
  {
    id: 5,
    title: 'Follow-up',
    start: new Date(date.getFullYear(), date.getMonth() + 1, -13),
    end: new Date(date.getFullYear(), date.getMonth() + 1, -12),
    extendedProps: {
      calendar: 'ETC'
    }
  },
  {
    id: 6,
    title: 'Follow-up',
    start: new Date(date.getFullYear(), date.getMonth() + 1, -13),
    end: new Date(date.getFullYear(), date.getMonth() + 1, -12),
    extendedProps: {
      calendar: 'Personal'
    }
  },
  {
    id: 7,
    title: 'Neuralscan',
    start: new Date(date.getFullYear(), date.getMonth() + 1, -13),
    end: new Date(date.getFullYear(), date.getMonth() + 1, -12),
    extendedProps: {
      calendar: 'Family'
    }
  },
  {
    id: 8,
    title: 'Follow-up',
    start: new Date(date.getFullYear(), date.getMonth() + 1, -13),
    end: new Date(date.getFullYear(), date.getMonth() + 1, -12),
    extendedProps: {
      calendar: 'Business'
    }
  },
  {
    id: 9,
    title: 'Follow-up',
    start: nextMonth,
    end: nextMonth,
    extendedProps: {
      calendar: 'Business'
    }
  },
  {
    id: 10,
    title: 'Follow-up',
    start: prevMonth,
    end: prevMonth,
    extendedProps: {
      calendar: 'Personal'
    }
  }
];
