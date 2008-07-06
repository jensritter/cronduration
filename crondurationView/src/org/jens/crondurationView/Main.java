package org.jens.crondurationView;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import javax.swing.UIManager;
import javax.swing.UnsupportedLookAndFeelException;

import org.jens.Shorthand.JDBC;
import org.jfree.chart.ChartFactory;
import org.jfree.chart.ChartPanel;
import org.jfree.chart.JFreeChart;
import org.jfree.chart.plot.CategoryPlot;
import org.jfree.chart.renderer.category.CategoryItemRenderer;
import org.jfree.data.gantt.SlidingGanttCategoryDataset;
import org.jfree.data.gantt.Task;
import org.jfree.data.gantt.TaskSeries;
import org.jfree.data.gantt.TaskSeriesCollection;
import org.jfree.data.time.SimpleTimePeriod;

public class Main extends NetbeansView{

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		try {
			UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
		} catch (ClassNotFoundException e) {
		} catch (InstantiationException e) {
		} catch (IllegalAccessException e) {
		} catch (UnsupportedLookAndFeelException e) {
		}
		java.awt.EventQueue.invokeLater(new Runnable() {
			public void run() {
				new Main().setVisible(true);
			}
		});
	}

	public Main() {
		JFreeChart chart = createChart(createDataset());
		ChartPanel chartPanel = new ChartPanel(chart, true);
		chartPanel.setPreferredSize(new Dimension(500, 270));
		panelCenter.add(chartPanel,BorderLayout.CENTER);
		this.pack();
	}
	private JFreeChart createChart(final TaskSeriesCollection dataset) {
		//SlidingGanttCategoryDataset a = new SlidingGanttCategoryDataset(dataset,0,5);
		final JFreeChart chart = ChartFactory.createGanttChart(
				"Gantt Chart Demo",  // chart title
				"Task",              // domain axis label
				"Date",              // range axis label
				dataset,             // data
				true,                // include legend
				true,                // tooltips
				false                // urls
		);
		final CategoryPlot plot = (CategoryPlot) chart.getPlot();
		//chart.getCategoryPlot().getDomainAxis().setMaxCategoryLabelWidthRatio(10.0f);
		final CategoryItemRenderer renderer = plot.getRenderer();
        renderer.setSeriesPaint(0, Color.blue);
		return chart;
	}

	public static TaskSeriesCollection createDataset() {

		final TaskSeries s1 = new TaskSeries("Scheduled");
		final TaskSeries s2 = new TaskSeries("Marked");

		try {
			Connection con = JDBC.openPGConnection("matrix","post","post","post");
			Statement stm = con.createStatement();
			ResultSet rs = stm.executeQuery("select * from parsed order by start");
			while (rs.next()) {
				s1.add(new Task(rs.getString("host") + "- " + rs.getString("command") + "." + rs.getString("id"),
						new SimpleTimePeriod(rs.getTimestamp("start"), rs.getTimestamp("stop"))));
				s2.add(new Task("X",
						new SimpleTimePeriod(rs.getTimestamp("start"), rs.getTimestamp("stop"))));
			}
			rs.close();
			stm.close();
			con.close();

		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		final TaskSeriesCollection collection = new TaskSeriesCollection();
		collection.add(s1);
		collection.add(s2);


		return collection;
	}
}
