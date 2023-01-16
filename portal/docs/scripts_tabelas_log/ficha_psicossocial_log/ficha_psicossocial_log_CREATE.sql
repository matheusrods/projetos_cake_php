
USE [RHHealth]
GO

/****** Object:  Table [dbo].[ficha_psicossocial_log]    Script Date: 04/01/2023 15:19:10 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[ficha_psicossocial_log](
	[codigo] [int] IDENTITY(1,1) NOT NULL,
	[codigo_pedido_exame] [int] NOT NULL,
	[codigo_ficha_psicossocial] [int] NOT NULL,
	[acao_sistema] [tinyint] NOT NULL,
	[total_sim] [varchar](2) NULL,
	[total_nao] [varchar](2) NULL,
	[codigo_empresa] [int] NULL,
	[ativo] [bit] NULL,
	[data_inclusao] [datetime] NULL,
	[codigo_usuario_inclusao] [int] NULL,
	[codigo_usuario_alteracao] [int] NULL,
	[codigo_medico] [int] NULL,
	[data_alteracao] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[codigo] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 90, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[codigo] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[codigo_pedido_exame] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[total_sim] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[total_nao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[codigo_empresa] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[ativo] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[data_inclusao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[codigo_usuario_inclusao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[codigo_usuario_alteracao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[codigo_medico] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_log].[data_alteracao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ALTER TABLE [dbo].[ficha_psicossocial_log]  WITH CHECK ADD  CONSTRAINT [fk_ficha_psicossocial_log_codigo_pedido_exame] FOREIGN KEY([codigo_pedido_exame])
REFERENCES [dbo].[pedidos_exames] ([codigo])
GO

ALTER TABLE [dbo].[ficha_psicossocial_log] CHECK CONSTRAINT [fk_ficha_psicossocial_log_codigo_pedido_exame]
GO

ALTER TABLE [dbo].[ficha_psicossocial_log]  WITH CHECK ADD  CONSTRAINT [fk_ficha_psicossocial_log_codigo_ficha_psicossocial] FOREIGN KEY([codigo_ficha_psicossocial])
REFERENCES [dbo].[ficha_psicossocial] ([codigo])
GO

ALTER TABLE [dbo].[ficha_psicossocial_log] CHECK CONSTRAINT [fk_ficha_psicossocial_log_codigo_ficha_psicossocial]
GO

ALTER TABLE [dbo].[ficha_psicossocial_log]  WITH CHECK ADD FOREIGN KEY([codigo_medico])
REFERENCES [dbo].[medicos] ([codigo])
GO
