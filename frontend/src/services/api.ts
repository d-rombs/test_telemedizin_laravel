import axios from 'axios';
import { 
  Specialization, 
  Doctor, 
  TimeSlot, 
  Appointment, 
  TimeSlotCreationParams 
} from '../types';

const API_URL = 'http://localhost:8001/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Specialization API
export const getSpecializations = async (): Promise<Specialization[]> => {
  const response = await api.get('/specializations');
  return response.data;
};

// Doctor API
export const getDoctors = async (): Promise<Doctor[]> => {
  const response = await api.get('/doctors');
  return response.data;
};

export const searchDoctors = async (query: string): Promise<Doctor[]> => {
  const response = await api.get(`/doctors/search?query=${query}`);
  return response.data;
};

export const getDoctor = async (id: number): Promise<Doctor> => {
  const response = await api.get(`/doctors/${id}`);
  return response.data;
};

// TimeSlot API
export const getAvailableTimeSlots = async (doctorId: number): Promise<TimeSlot[]> => {
  const response = await api.get(`/doctors/${doctorId}/time-slots`);
  return response.data;
};

export const checkTimeSlotAvailability = async (id: number): Promise<{ is_available: boolean }> => {
  const response = await api.get(`/time-slots/${id}/check-availability`);
  return response.data;
};

export const createMultipleTimeSlots = async (params: TimeSlotCreationParams): Promise<TimeSlot[]> => {
  const response = await api.post('/time-slots/create-multiple', params);
  return response.data;
};

// Appointment API
export const createAppointment = async (appointment: Partial<Appointment>, timeSlotId: number): Promise<Appointment> => {
  const response = await api.post('/appointments', {
    ...appointment,
    time_slot_id: timeSlotId,
  });
  return response.data;
};

export const getAppointmentsByEmail = async (email: string): Promise<Appointment[]> => {
  const response = await api.post('/appointments/by-email', { email });
  return response.data;
};

export const cancelAppointment = async (id: number): Promise<Appointment> => {
  const response = await api.put(`/appointments/${id}/cancel`);
  return response.data;
};

export default api; 