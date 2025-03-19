import React, { useState } from 'react';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import DoctorList from './components/DoctorList';
import TimeSlotList from './components/TimeSlotList';
import AppointmentForm from './components/AppointmentForm';
import AppointmentManager from './components/AppointmentManager';
import { Doctor, TimeSlot, Appointment } from './types';
import './App.css';

const BookingPage: React.FC = () => {
  const [selectedDoctor, setSelectedDoctor] = useState<Doctor | null>(null);
  const [selectedTimeSlot, setSelectedTimeSlot] = useState<TimeSlot | null>(null);
  const [bookingStep, setBookingStep] = useState<'doctor' | 'timeSlot' | 'form'>('doctor');
  const [bookedAppointment, setBookedAppointment] = useState<Appointment | null>(null);

  const handleDoctorSelect = (doctor: Doctor) => {
    setSelectedDoctor(doctor);
    setSelectedTimeSlot(null);
    setBookingStep('timeSlot');
  };

  const handleTimeSlotSelect = (timeSlot: TimeSlot) => {
    setSelectedTimeSlot(timeSlot);
    setBookingStep('form');
  };

  const handleAppointmentCreated = (appointment: Appointment) => {
    setBookedAppointment(appointment);
  };

  const handleReset = () => {
    setSelectedDoctor(null);
    setSelectedTimeSlot(null);
    setBookedAppointment(null);
    setBookingStep('doctor');
  };

  return (
    <div className="booking-page">
      <h1>Telemedizinische Terminbuchung</h1>
      
      <div className="booking-steps">
        <div className={`step ${bookingStep === 'doctor' ? 'active' : ''}`}>
          1. Arzt auswählen
        </div>
        <div className={`step ${bookingStep === 'timeSlot' ? 'active' : ''}`}>
          2. Termin auswählen
        </div>
        <div className={`step ${bookingStep === 'form' ? 'active' : ''}`}>
          3. Termin buchen
        </div>
      </div>
      
      <div className="booking-container">
        {bookingStep === 'doctor' && (
          <DoctorList onSelectDoctor={handleDoctorSelect} />
        )}
        
        {bookingStep === 'timeSlot' && (
          <div>
            <button onClick={handleReset} className="back-button">
              Zurück zur Arztauswahl
            </button>
            <TimeSlotList 
              doctor={selectedDoctor} 
              onSelectTimeSlot={handleTimeSlotSelect} 
            />
          </div>
        )}
        
        {bookingStep === 'form' && (
          <div>
            <button onClick={() => setBookingStep('timeSlot')} className="back-button">
              Zurück zur Terminauswahl
            </button>
            <AppointmentForm 
              doctor={selectedDoctor}
              selectedTimeSlot={selectedTimeSlot}
              onAppointmentCreated={handleAppointmentCreated}
              onCancel={handleReset}
            />
          </div>
        )}
      </div>
    </div>
  );
};

const App: React.FC = () => {
  return (
    <Router>
      <div className="app">
        <header className="app-header">
          <h1>Telemedizinisches Terminplanungssystem</h1>
          <nav>
            <ul>
              <li>
                <Link to="/">Termin buchen</Link>
              </li>
              <li>
                <Link to="/manage">Termine verwalten</Link>
              </li>
            </ul>
          </nav>
        </header>
        
        <main className="app-content">
          <Routes>
            <Route path="/" element={<BookingPage />} />
            <Route path="/manage" element={<AppointmentManager />} />
          </Routes>
        </main>
        
        <footer className="app-footer">
          <p>&copy; 2025 Telemedizinisches Terminplanungssystem</p>
        </footer>
      </div>
    </Router>
  );
};

export default App;
