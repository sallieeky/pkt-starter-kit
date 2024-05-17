import moment from 'moment';

moment.locale = 'id';

export default class DatetimeFormatter {
    static formatDateHumanReadable(strDate) {
        const date = new Date(strDate);
        const relativeTime = moment(date).calendar(null, {
            sameDay: '[Today]',
            lastDay: '[Yesterday]',
            lastWeek: '[Last] dddd',
            sameElse: 'DD MMMM YYYY'
        });
        return relativeTime;
    }

    static formatDateCalender(strDate) {
        return moment(strDate).format('DD/MM/YYYY');
    }
    
    static formatTime(strDate) {
        return moment(strDate).format('HH:mm');
    }
    
    static formatDatetime(strDate){
        return moment(strDate).format('DD/MM/YYYY HH:mm');
    }
}
