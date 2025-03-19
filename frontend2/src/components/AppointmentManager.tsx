import React, { useState } from 'react';
import { Appointment } from '../types';
import { getAppointmentsByEmail, cancelAppointment } from '../services/api';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';

const AppointmentManager: React.FC = () => {
  const [email, setEmail] = useState<string>('');
  const [appointments, setAppointments] = useState<Appointment[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);

  const handleSearch = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!email.trim()) {
      setError('Bitte geben Sie eine E-Mail-Adresse ein.');
      return;
    }
    
    try {
      setLoading(true);
      setError(null);
      setSuccess(null);
      
      const data = await getAppointmentsByEmail(email);
      setAppointments(data);
      
      if (data.length === 0) {
        setSuccess('Keine Termine für diese E-Mail-Adresse gefunden.');
      }
    } catch (err) {
      setError('Fehler beim Abrufen der Termine. Bitte versuchen Sie es später erneut.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleCancel = async (appointmentId: number) => {
    if (!window.confirm('Sind Sie sicher, dass Sie diesen Termin stornieren möchten?')) {
      return;
    }
    
    try {
      setLoading(true);
      setError(null);
      setSuccess(null);
      
      await cancelAppointment(appointmentId);
      
      // Update the appointments list
      const updatedAppointments = await getAppointmentsByEmail(email);
      setAppointments(updatedAppointments);
      
      setSuccess('Termin erfolgreich storniert.');
    } catch (err) {
      setError('Fehler beim Stornieren des Termins. Bitte versuchen Sie es später erneut.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const getStatusText = (status: string): string => {
    switch (status) {
      case 'scheduled':
        return 'Geplant';
      case 'completed':
        return 'Abgeschlossen';
      case 'cancelled':
        return 'Storniert';
      default:
        return status;
    }
  };

  return (
    <div className="appointment-manager">
      <h2>Termine verwalten</h2>
      
      <form onSubmit={handleSearch} className="email-search-form">
        <div className="form-group">
          <label htmlFor="email">E-Mail-Adresse:</label>
          <input
            type="email"
            id="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>
        
        <button type="submit" disabled={loading}>
          {loading ? 'Suche...' : 'Termine suchen'}
        </button>
      </form>
      
      {error && <p className="error">{error}</p>}
      {success && <p className="success">{success}</p>}
      
      {appointments.length > 0 && (
        <div className="appointments-list">
          <h3>Ihre Termine</h3>
          
          <table>
            <thead>
              <tr>
                <th>Datum</th>
                <th>Uhrzeit</th>
                <th>Arzt</th>
                <th>Fachgebiet</th>
                <th>Status</th>
                <th>Aktionen</th>
              </tr>
            </thead>
            <tbody>
              {appointments.map(appointment => (
                <tr key={appointment.id}>
                  <td>{format(new Date(appointment.date_time), 'dd.MM.yyyy', { locale: de })}</td>
                  <td>{format(new Date(appointment.date_time), 'HH:mm', { locale: de })}</td>
                  <td>{appointment.doctor?.name}</td>
                  <td>{appointment.doctor?.specialization?.name}</td>
                  <td>{getStatusText(appointment.status)}</td>
                  <td>
                    {appointment.status === 'scheduled' && (
                      <button 
                        onClick={() => handleCancel(appointment.id)}
                        disabled={loading}
                      >
                        Stornieren
                      </button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
};

export default AppointmentManager; 