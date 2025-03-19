import React, { useState, useEffect } from 'react';
import { TimeSlot, Doctor } from '../types';
import { getAvailableTimeSlots, checkTimeSlotAvailability } from '../services/api';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';

interface TimeSlotListProps {
  doctor: Doctor | null;
  onSelectTimeSlot: (timeSlot: TimeSlot) => void;
}

const TimeSlotList: React.FC<TimeSlotListProps> = ({ doctor, onSelectTimeSlot }) => {
  const [timeSlots, setTimeSlots] = useState<TimeSlot[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);
  const [selectedDate, setSelectedDate] = useState<string>('');
  const [availableDates, setAvailableDates] = useState<string[]>([]);

  useEffect(() => {
    if (!doctor) return;

    const fetchTimeSlots = async () => {
      try {
        setLoading(true);
        const data = await getAvailableTimeSlots(doctor.id);
        setTimeSlots(data);
        
        // Extract unique dates from time slots
        const datesSet = new Set<string>();
        data.forEach(slot => {
          datesSet.add(format(new Date(slot.start_time), 'yyyy-MM-dd'));
        });
        const dates = Array.from(datesSet);
        setAvailableDates(dates);
        
        if (dates.length > 0) {
          setSelectedDate(dates[0]);
        }
        
        setError(null);
      } catch (err) {
        setError('Fehler beim Laden der Zeitfenster. Bitte versuchen Sie es später erneut.');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchTimeSlots();
  }, [doctor]);

  const handleSelectTimeSlot = async (timeSlot: TimeSlot) => {
    try {
      // Check real-time availability before selecting
      const availability = await checkTimeSlotAvailability(timeSlot.id);
      
      if (!availability.is_available) {
        setError('Dieses Zeitfenster ist nicht mehr verfügbar. Bitte wählen Sie ein anderes.');
        
        // Refresh time slots
        if (doctor) {
          const data = await getAvailableTimeSlots(doctor.id);
          setTimeSlots(data);
        }
        
        return;
      }
      
      onSelectTimeSlot(timeSlot);
    } catch (err) {
      setError('Fehler bei der Verfügbarkeitsprüfung. Bitte versuchen Sie es später erneut.');
      console.error(err);
    }
  };

  const filteredTimeSlots = selectedDate
    ? timeSlots.filter(slot => 
        format(new Date(slot.start_time), 'yyyy-MM-dd') === selectedDate
      )
    : timeSlots;

  if (!doctor) {
    return <p>Bitte wählen Sie zuerst einen Arzt aus.</p>;
  }

  return (
    <div className="time-slot-list">
      <h2>Verfügbare Termine für {doctor.name}</h2>
      
      {loading && <p>Laden...</p>}
      {error && <p className="error">{error}</p>}
      
      {availableDates.length > 0 && (
        <div className="date-selector">
          <label>
            Datum auswählen:
            <select
              value={selectedDate}
              onChange={(e) => setSelectedDate(e.target.value)}
            >
              {availableDates.map(date => (
                <option key={date} value={date}>
                  {format(new Date(date), 'EEEE, d. MMMM yyyy', { locale: de })}
                </option>
              ))}
            </select>
          </label>
        </div>
      )}
      
      <div className="time-slots">
        {filteredTimeSlots.length > 0 ? (
          filteredTimeSlots.map(slot => (
            <div 
              key={slot.id} 
              className="time-slot-item"
              onClick={() => handleSelectTimeSlot(slot)}
            >
              <p>
                {format(new Date(slot.start_time), 'HH:mm')} - 
                {format(new Date(slot.end_time), 'HH:mm')}
              </p>
            </div>
          ))
        ) : (
          <p>Keine verfügbaren Zeitfenster für diesen Tag.</p>
        )}
      </div>
    </div>
  );
};

export default TimeSlotList; 