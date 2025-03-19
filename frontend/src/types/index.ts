export interface Specialization {
  id: number;
  name: string;
  created_at?: string;
  updated_at?: string;
}

export interface Doctor {
  id: number;
  name: string;
  specialization_id: number;
  specialization?: Specialization;
  created_at?: string;
  updated_at?: string;
}

export interface TimeSlot {
  id: number;
  doctor_id: number;
  start_time: string;
  end_time: string;
  is_available: boolean;
  doctor?: Doctor;
  created_at?: string;
  updated_at?: string;
}

export interface Appointment {
  id: number;
  doctor_id: number;
  patient_name: string;
  patient_email: string;
  date_time: string;
  status: 'scheduled' | 'completed' | 'cancelled';
  doctor?: Doctor;
  created_at?: string;
  updated_at?: string;
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}

export interface TimeSlotCreationParams {
  doctor_id: number;
  date: string;
  start_hour: number;
  end_hour: number;
  slot_duration: number;
} 