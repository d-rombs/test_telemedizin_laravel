import React, { useState } from 'react';
import { Doctor, TimeSlot, Appointment } from '../types';
import { createAppointment } from '../services/api';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';

interface AppointmentFormProps {
  doctor: Doctor | null;
  selectedTimeSlot: TimeSlot | null;
  onAppointmentCreated: (appointment: Appointment) => void;
  onCancel: () => void;
}

const AppointmentForm: React.FC<AppointmentFormProps> = ({
  doctor,
  selectedTimeSlot,
  onAppointmentCreated,
  onCancel
}) => {
  const [patientName, setPatientName] = useState<string>('');
  const [patientEmail, setPatientEmail] = useState<string>('');
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<boolean>(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!doctor || !selectedTimeSlot) {
      setError('Bitte wählen Sie einen Arzt und ein Zeitfenster aus.');
      return;
    }
    
    if (!patientName.trim() || !patientEmail.trim()) {
      setError('Bitte füllen Sie alle Felder aus.');
      return;
    }
    
    try {
      setLoading(true);
      setError(null);
      
      const appointmentData: Partial<Appointment> = {
        doctor_id: doctor.id,
        patient_name: patientName,
        patient_email: patientEmail,
        date_time: selectedTimeSlot.start_time,
      };
      
      const createdAppointment = await createAppointment(appointmentData, selectedTimeSlot.id);
      
      setSuccess(true);
      onAppointmentCreated(createdAppointment);
      
      // Reset form
      setPatientName('');
      setPatientEmail('');
    } catch (err: any) {
      if (err.response && err.response.data && err.response.data.message) {
        setError(err.response.data.message);
      } else {
        setError('Fehler bei der Terminbuchung. Bitte versuchen Sie es später erneut.');
      }
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  if (!doctor || !selectedTimeSlot) {
    return <p>Bitte wählen Sie einen Arzt und ein Zeitfenster aus.</p>;
  }

  if (success) {
    return (
      <div className="appointment-success">
        <h2>Termin erfolgreich gebucht!</h2>
        <p>
          Vielen Dank für Ihre Buchung. Eine Bestätigungsmail wurde an {patientEmail} gesendet.
        </p>
        <button onClick={onCancel}>Zurück zur Arztauswahl</button>
      </div>
    );
  }

  return (
    <div className="appointment-form">
      <h2>Termin buchen</h2>
      
      <div className="appointment-details">
        <p><strong>Arzt:</strong> {doctor.name}</p>
        <p><strong>Fachgebiet:</strong> {doctor.specialization?.name}</p>
        <p><strong>Datum:</strong> {format(new Date(selectedTimeSlot.start_time), 'EEEE, d. MMMM yyyy', { locale: de })}</p>
        <p><strong>Uhrzeit:</strong> {format(new Date(selectedTimeSlot.start_time), 'HH:mm')} - {format(new Date(selectedTimeSlot.end_time), 'HH:mm')}</p>
      </div>
      
      {error && <p className="error">{error}</p>}
      
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label htmlFor="patientName">Name:</label>
          <input
            type="text"
            id="patientName"
            value={patientName}
            onChange={(e) => setPatientName(e.target.value)}
            required
          />
        </div>
        
        <div className="form-group">
          <label htmlFor="patientEmail">E-Mail:</label>
          <input
            type="email"
            id="patientEmail"
            value={patientEmail}
            onChange={(e) => setPatientEmail(e.target.value)}
            required
          />
        </div>
        
        <div className="form-actions">
          <button type="button" onClick={onCancel} disabled={loading}>
            Abbrechen
          </button>
          <button type="submit" disabled={loading}>
            {loading ? 'Wird gebucht...' : 'Termin buchen'}
          </button>
        </div>
      </form>
    </div>
  );
};

export default AppointmentForm; 